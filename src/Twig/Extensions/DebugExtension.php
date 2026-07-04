<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig\Twig\Extensions;

use Tempest\Core\EnvironmentVariableValidationFailed;
use Twig\Attribute\AsTwigFunction;

use function Tempest\env;
use function Tempest\root_path;

/**
 * The Twig extension for debugging and utility functions.
 */
class DebugExtension
{
    /**
     * Dump - dumps variables without stopping execution.
     *
     * @param mixed ...$vars Values to dump.
     * @return void
     */
    #[AsTwigFunction('dump')]
    public static function dump(mixed $vars): void
    {
        dump($vars);
    }

    /**
     * @param mixed $data Values to dump
     * @return void
     */
    #[AsTwigFunction("dd")]
    public static function dd(mixed $data): void
    {
        dd($data);
    }

    /**
     * Get the class name of an object.
     *
     * @param object $object Object to inspect.
     * @return string Class name of the object.
     */
    #[AsTwigFunction('class')]
    public static function getClass(object $object): string
    {
        return get_class($object);
    }

    /**
     * Check if a variable is empty.
     *
     * @param mixed $var Value to check.
     * @return bool True when the value is empty.
     */
    #[AsTwigFunction('is_empty')]
    public static function isEmpty(mixed $var): bool
    {
        return empty($var);
    }

    /**
     * Get the type of variable.
     *
     * @param mixed $var Value to inspect.
     * @return string PHP type name.
     */
    #[AsTwigFunction('get_type')]
    public static function getType(mixed $var): string
    {
        return gettype($var);
    }

    /**
     * Get environment variable.
     *
     * @param string $key Environment key to read.
     * @param mixed $default Fallback when the key is missing or null.
     * @return mixed Environment value or fallback.
     * @throws EnvironmentVariableValidationFailed
     */
    #[AsTwigFunction('env')]
    public static function env(string $key, mixed $default = null): mixed
    {
        return env($key, $default);
    }

    /**
     * Get an absolute path scoped to the root of the project.
     *
     * @param string ...$parts Path segments to append to the project root.
     * @return string Absolute path.
     */
    #[AsTwigFunction('root_path')]
    public static function rootPath(string ...$parts): string
    {
        return root_path(...$parts);
    }

    /**
     * Convert a value to JSON.
     *
     * @param mixed $value Value to encode.
     * @param int $flags JSON encoding flags.
     * @return string|false Encoded JSON string or false on failure.
     */
    #[AsTwigFunction('to_json')]
    public static function toJson(mixed $value, int $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE): string|false
    {
        return json_encode($value, $flags);
    }

    /**
     * Count elements in an array or countable object.
     *
     * @param mixed $var Value to count.
     * @return int Element count, or zero when not countable.
     */
    #[AsTwigFunction('count')]
    public static function count(mixed $var): int
    {
        if (!is_countable($var)) {
            return 0;
        }

        return count($var);
    }

    /**
     * Get the current URL (simplified version).
     *
     * @return mixed Current request URI or "/" when unavailable.
     */
    #[AsTwigFunction('current_url')]
    public static function currentUrl(): mixed
    {
        if (!array_key_exists('REQUEST_URI', $_SERVER) || $_SERVER['REQUEST_URI'] === null) {
            return '/';
        }

        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Get current timestamp.
     *
     * @return int Current Unix timestamp.
     */
    #[AsTwigFunction('now')]
    public static function now(): int
    {
        return time();
    }
}
