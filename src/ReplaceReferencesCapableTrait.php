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
     * @param string             $input
     * @param ContainerInterface $container
     * @param string             $startDelimiter
     * @param string             $endDelimiter
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return string The resulting string.
     */
    protected function _replaceReferences($input, ContainerInterface $container, $startDelimiter = '${', $endDelimiter = '}')
    {
        $subject = $this->_normalizeString($input);

        preg_match_all('/\\' . $startDelimiter . '[^\\' . $endDelimiter . ']+' . $endDelimiter . '/', $subject, $matches);
        $tokens = $matches[0];

        foreach ($tokens as $token) {
            $token   = substr($token, 2, -1);
            $value   = $container->get($token);
            $subject = str_replace($token, $value, $subject);
        }

        return $subject;
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
