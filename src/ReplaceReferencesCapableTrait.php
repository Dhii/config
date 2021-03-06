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
 * Adds ability to replace all tokens wrapped with some delimiters in a string with corresponding values retrieved from a container.
 *
 * @since [*next-version*]
 */
trait ReplaceReferencesCapableTrait
{
    /**
     * Replaces all tokens wrapped with some delimiters in a string with corresponding values retrieved from a container.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable  $input          Input string to find and replace references
     * @param ContainerInterface $container      Container to retrieve values for replace.
     * @param string|Stringable  $startDelimiter Starting delimiter of token for replace
     * @param mixed              $default        String to replace reference if key not found in container.
     * @param string|Stringable  $endDelimiter   Ending delimiter of token for replace
     *
     * @throws ContainerExceptionInterface Error while retrieving the entry from the container.
     *
     * @return string The resulting string.
     */
    protected function _replaceReferences($input, ContainerInterface $container, $default = null, $startDelimiter = '${', $endDelimiter = '}')
    {
        $regexpDelimiter = '/';
        $input           = $this->_normalizeString($input);
        $defaultValue    = $default === null ? '' : $this->_normalizeString($default);

        $startDelimiter = preg_quote($this->_normalizeString($startDelimiter), $regexpDelimiter);
        $endDelimiter   = preg_quote($this->_normalizeString($endDelimiter), $regexpDelimiter);

        $regexp = $regexpDelimiter . $startDelimiter . '(.*?)' . $endDelimiter . $regexpDelimiter;

        preg_match_all($regexp, $input, $matches);

        foreach ($matches[0] as $i => $token) {
            $key = $matches[1][$i];
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
