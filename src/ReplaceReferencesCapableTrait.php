<?php

namespace Dhii\Config;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Trait ReplaceReferencesCapableTrait.
 *
 * Adds ability to replace all tokens in the form `${key}` in a string with corresponding values retrieved from a container.
 *
 * @since [*next-version*]
 */
trait ReplaceReferencesCapableTrait
{
    /**
     * Replaces all tokens in the form `${key}` in a string with corresponding values retrieved from a container.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable  $input          Input string to find and replace references
     * @param ContainerInterface $container
     * @param string|Stringable  $startDelimiter
     * @param mixed              $default        String to replace reference if key not found in container.
     * @param string|Stringable  $endDelimiter
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return string The resulting string.
     */
    protected function _replaceReferences($input, ContainerInterface $container, $default = null, $startDelimiter = '${', $endDelimiter = '}')
    {
        $input        = $this->_normalizeString($input);
        $defaultValue = $default === null ? '' : $this->_normalizeString($default);

        $startDelimiter = preg_quote($this->_normalizeString($startDelimiter));
        $endDelimiter   = preg_quote($this->_normalizeString($endDelimiter));

        $regexp = '/' . $startDelimiter . '(.*?)' . $endDelimiter . '/';

        preg_match_all($regexp, $input, $matches);

        for ($i = 0; $i < count($matches[0]); ++$i) {
            $token = $matches[0][$i];
            $key   = $matches[1][$i];
            try {
                $value = $container->get($key);
            } catch (NotFoundExceptionInterface $e) {
                $value = $defaultValue;
            }
            $input = str_replace($token, $value, $input);
        }

        return $input;
    }

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);
}
