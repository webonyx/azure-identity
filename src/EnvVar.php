<?php

namespace Azure\Identity;

final class EnvVar
{
    public static function get(string $name): ?string
    {
        if (isset($_ENV[$name])) {
            // variable_order = *E*GPCS
            return (string) $_ENV[$name];
        } elseif (isset($_SERVER[$name]) && !\is_array($_SERVER[$name]) && !str_starts_with($name, 'HTTP_')) {
            // fastcgi_param, env var, ...
            return (string) $_SERVER[$name];
        } elseif (false === $env = getenv($name)) {
            // getenv not thread safe
            return null;
        }

        return $env;
    }
}
