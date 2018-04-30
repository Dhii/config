<?php

namespace Dhii\Config;

use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Exception as RootException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Functionality for de-referencing tokens in a string.
 *
 * @since [*next-version*]
 */
trait DereferenceTokensCapableTrait
{
    /**
     * Replaces tokens with their values.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|mixed $value The value, in which tokens may be found.
     * If value is not stringable, will return it as is.
     *
     * @throws RuntimeException If tokens could not be replaced.
     *
     * @return string|Stringable The value with tokens replaced.
     */
    protected function _dereferenceTokens($value)
    {
        if (is_scalar($value) && !is_string($value)) {
            return $value;
        }

        try {
            $value = $this->_normalizeString($value);
        }
        catch (InvalidArgumentException $e) {
            return $value;
        }

        $container = $this->_getContainer();
        $tokenStart = $this->_getTokenStart();
        $tokenEnd = $this->_getTokenEnd();

        try {
            $value = $this->_replaceReferences($value, $container, null, $tokenStart, $tokenEnd);
        }
        catch (ContainerExceptionInterface $e) {
            throw $this->_createRuntimeException($this->__('Could not de-reference tokens'), null, $e);
        }

        return $value;
    }

    /**
     * Replaces all tokens wrapped with some delimiters in a string with corresponding values retrieved from a container.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable  $input          Input string to find and replace references
     * @param BaseContainerInterface $container      Container to retrieve values for replace.
     * @param string|Stringable  $startDelimiter Starting delimiter of token for replace
     * @param mixed              $default        String to replace reference if key not found in container.
     * @param string|Stringable  $endDelimiter   Ending delimiter of token for replace
     *
     * @throws ContainerExceptionInterface Error while retrieving an entry from the container.
     *
     * @return string The resulting string.
     */
    abstract protected function _replaceReferences(
        $input,
        BaseContainerInterface $container,
        $default = null,
        $startDelimiter = '${',
        $endDelimiter = '}'
    );

    /**
     * Retrieves the container associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return BaseContainerInterface|null The container, if any.
     */
    abstract protected function _getContainer();

    /**
     * Retrieves the token end delimiter.
     *
     * @since [*next-version*]
     *
     * @return Stringable|null|string The delimiter
     */
    abstract public function _getTokenEnd();

    /**
     * Retrieves the token start delimiter.
     *
     * @since [*next-version*]
     *
     * @return Stringable|null|string The delimiter.
     */
    abstract public function _getTokenStart();

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

    /**
     * Creates a new Runtime exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return RuntimeException The new exception.
     */
    abstract protected function _createRuntimeException($message = null, $code = null, $previous = null);

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = array(), $context = null);
}
