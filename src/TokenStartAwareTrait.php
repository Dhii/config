<?php

namespace Dhii\Config;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Functionality for awareness of a token start delimiter.
 *
 * @since [*next-version*]
 */
trait TokenStartAwareTrait
{
    /**
     * @since [*next-version*]
     *
     * @var string|Stringable|null
     */
    protected $tokenStart;

    /**
     * Retrieves the token start delimiter.
     *
     * @since [*next-version*]
     *
     * @return Stringable|null|string The delimiter.
     */
    public function _getTokenStart()
    {
        return $this->tokenStart;
    }

    /**
     * Assigns the token start delimiter.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $delimiter The delimiter that marks the start of a token.
     *
     * @throws InvalidArgumentException If the delimiter is invalid.
     */
    public function _setTokenStart($delimiter)
    {
        if (
            !($delimiter instanceof Stringable) &&
            !is_string($delimiter) &&
            !is_null($delimiter)
        ) {
            throw $this->_createInvalidArgumentException($this->__('Invalid token start delimiter'), null, null, $delimiter);
        }

        $this->tokenStart = $delimiter;
    }

    /**
     * Creates a new Invalid Argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

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
    abstract protected function __($string, $args = [], $context = null);
}
