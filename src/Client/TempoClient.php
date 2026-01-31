<?php

namespace NinoHaar\Tempo\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use JsonMapper\JsonMapper;
use NinoHaar\Tempo\Client\Authenticators\BearerTokenAuthenticator;
use NinoHaar\Tempo\Client\Authenticators\OAuth2Authenticator;
use NinoHaar\Tempo\Configuration\ConfigurationInterface;
use NinoHaar\Tempo\Exceptions\AuthenticationException;
use NinoHaar\Tempo\Exceptions\RateLimitException;
use NinoHaar\Tempo\Exceptions\TempoException;
use Psr\Http\Message\ResponseInterface;

class TempoClient
{
    private GuzzleClient $client;

    private BearerTokenAuthenticator|OAuth2Authenticator $authenticator;

    private JsonMapper $jsonMapper;

    public function __construct(
        private ConfigurationInterface $config,
        private EnvFileUpdater $envUpdater,
    ) {
        $this->initializeClient();
        $this->initializeAuthenticator();
        $this->jsonMapper = new JsonMapper();
    }

    private function initializeClient(): void
    {
        $this->client = new GuzzleClient([
            'base_uri' => $this->buildBaseUrl(),
            'timeout' => $this->config->getHttpTimeout(),
            'connect_timeout' => $this->config->getHttpConnectTimeout(),
            'verify' => true,
        ]);
    }

    private function initializeAuthenticator(): void
    {
        if ($this->config->getAuthType() === 'oauth2') {
            $this->authenticator = new OAuth2Authenticator($this->config, $this->envUpdater);
        } else {
            $this->authenticator = new BearerTokenAuthenticator($this->config);
        }
    }

    private function buildBaseUrl(): string
    {
        return $this->config->getBaseUrl().'/'.$this->config->getApiVersion();
    }

    /**
     * Make a GET request to the API.
     *
     * @param  string  $endpoint  The API endpoint (without leading slash)
     * @param  array  $query  Query parameters
     * @return array
     */
    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * Make a POST request to the API.
     *
     * @param  string  $endpoint  The API endpoint (without leading slash)
     * @param  array  $data  Request body data
     * @return array
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Make a PUT request to the API.
     *
     * @param  string  $endpoint  The API endpoint (without leading slash)
     * @param  array  $data  Request body data
     * @return array
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * Make a DELETE request to the API.
     *
     * @param  string  $endpoint  The API endpoint (without leading slash)
     * @return array
     */
    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Make an HTTP request with retry logic and authentication.
     *
     * @param  string  $method
     * @param  string  $endpoint
     * @param  array  $options
     * @return array
     */
    private function request(string $method, string $endpoint, array $options = []): array
    {
        $maxRetries = $this->config->getRetryTimes();
        $retryDelay = $this->config->getRetrySleep();

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                $request = new Request($method, $endpoint);

                // Authenticate request
                $request = $this->authenticator->authenticate($request);

                // Merge default headers and options
                $options['headers'] = array_merge(
                    $options['headers'] ?? [],
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ]
                );

                $response = $this->client->send($request, $options);

                return $this->parseResponse($response);
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() === 401) {
                    // Try to refresh token and retry
                    if ($this->authenticator instanceof OAuth2Authenticator && $attempt < $maxRetries) {
                        try {
                            $this->authenticator->refreshAccessToken();
                            continue;
                        } catch (\Throwable $refreshException) {
                            throw new AuthenticationException(
                                'Authentication failed: '.$e->getResponse()->getReasonPhrase(),
                                $e->getCode(),
                                $e
                            );
                        }
                    }
                    throw new AuthenticationException(
                        'Authentication failed: '.$e->getResponse()->getReasonPhrase(),
                        $e->getCode(),
                        $e
                    );
                } elseif ($e->getResponse()->getStatusCode() === 429) {
                    // Rate limited
                    $retryAfter = (int) ($e->getResponse()->getHeaderLine('Retry-After') ?? 60);
                    throw new RateLimitException($retryAfter);
                } elseif ($e->getResponse()->getStatusCode() === 404) {
                    // Not found
                    throw new TempoException(
                        'Resource not found: '.$e->getResponse()->getReasonPhrase(),
                        404,
                        $e
                    );
                } else {
                    // Other client error
                    throw new TempoException(
                        'API error: '.$e->getResponse()->getReasonPhrase(),
                        $e->getCode(),
                        $e
                    );
                }
            } catch (ConnectException|RequestException $e) {
                if ($attempt < $maxRetries) {
                    $delayMs = $retryDelay * pow(2, $attempt); // Exponential backoff
                    usleep($delayMs * 1000);
                    continue;
                }
                throw new TempoException('Connection failed: '.$e->getMessage(), $e->getCode(), $e);
            }
        }

        throw new TempoException('Request failed after '.$maxRetries.' retries');
    }

    /**
     * Parse API response.
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();

        // No content
        if ($statusCode === 204) {
            return [];
        }

        $body = (string) $response->getBody();

        if (empty($body)) {
            return [];
        }

        $decoded = json_decode($body, true, flags: JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Map response data to model class.
     *
     * @template T of object
     * @param  array  $data
     * @param  class-string<T>  $class
     * @return T
     */
    public function mapToModel(array $data, string $class): object
    {
        return $this->jsonMapper->map((object) $data, new $class());
    }

    /**
     * Map array of response data to model class instances.
     *
     * @template T of object
     * @param  array  $items
     * @param  class-string<T>  $class
     * @return array<T>
     */
    public function mapToModels(array $items, string $class): array
    {
        return array_map(
            fn (array $item) => $this->mapToModel($item, $class),
            $items
        );
    }

    /**
     * Get the JsonMapper instance.
     */
    public function getJsonMapper(): JsonMapper
    {
        return $this->jsonMapper;
    }
}
