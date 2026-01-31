<?php

namespace NinoHaar\Tempo\Client;

use Illuminate\Support\Str;

class EnvFileUpdater
{
    private string $envPath;

    public function __construct()
    {
        $this->envPath = base_path('.env');
    }

    /**
     * Update .env file with new values.
     *
     * @param  array<string, string|int>  $values
     */
    public function update(array $values): void
    {
        if (! file_exists($this->envPath)) {
            return;
        }

        $content = file_get_contents($this->envPath);

        foreach ($values as $key => $value) {
            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $content)) {
                // Update existing key
                $content = preg_replace(
                    $pattern,
                    "{$key}=".$this->formatValue($value),
                    $content
                );
            } else {
                // Append new key
                $content .= "\n{$key}=".$this->formatValue($value);
            }
        }

        file_put_contents($this->envPath, $content);

        // Reload config
        if (function_exists('cache')) {
            cache()->forget('tempo.env.updated');
        }
    }

    private function formatValue(string|int $value): string
    {
        $value = (string) $value;

        // Quote value if it contains spaces or special characters
        if (Str::contains($value, [' ', '=', '"', "'"])) {
            return '"'.addslashes($value).'"';
        }

        return $value;
    }
}
