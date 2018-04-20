<?php

namespace Dhii\Config;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;

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
     * @param string|Stringable  $input
     * @param ContainerInterface $container
     * @param string|Stringable  $startDelimiter
     * @param string|Stringable  $endDelimiter
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return string The resulting string.
     */
    protected function _replaceReferences($input, ContainerInterface $container, $startDelimiter = '${', $endDelimiter = '}')
    {
        $input = $this->_normalizeString($input);

        $startDelimiter = $this->_normalizeRegexpDelimiter($startDelimiter);
        $endDelimiter   = $this->_normalizeRegexpDelimiter($endDelimiter);

        $regexp = '/' . $startDelimiter . '(.*?)' . $endDelimiter . '/';

        preg_match_all($regexp, $input, $matches);

        for ($i = 0; $i < count($matches[0]); ++$i) {
            $token = $matches[0][$i];
            $key   = $matches[1][$i];
            $input = str_replace($token, $container->get($key), $input);
        }

        return $input;
    }

    /**
     * Normalize regexp delimiter, so all chars passed will be recognized
     * as not special chars, and will not affect regular expression.
     *
     * For example, this `${` becomes `\$\{` and doesn't affect regular expression.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $delimiter
     *
     * @return string
     */
    protected function _normalizeRegexpDelimiter($delimiter)
    {
        $delimiter           = $this->_normalizeString($delimiter);
        $normalizedDelimiter = '';
        for ($i = 0; $i < strlen($delimiter); ++$i) {
            $normalizedDelimiter .= '\\' . $delimiter[$i];
        }

        return $normalizedDelimiter;
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
