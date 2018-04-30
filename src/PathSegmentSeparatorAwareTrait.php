<?php

namespace Dhii\Config;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

trait PathSegmentSeparatorAwareTrait
{
    /**
     * @since [*next-version*]
     *
     * @var string|Stringable|null
     */
    protected $pathSegmentSeparator;

    /**
     * Retrieves the separator of segments in a string path.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable|null The path separator.
     */
    protected function _getPathSegmentSeparator()
    {
        return $this->pathSegmentSeparator;
    }

    /**
     * Assigns the separator of segments in a string path.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $separator The separator.
     *
     * @throws InvalidArgumentException If the separator is invalid.
     */
    protected function _setPathSegmentSeparator($separator)
    {
        if (
            !($separator instanceof Stringable) &&
            !is_string($separator) &&
            !is_null($separator)
        ) {
            throw $this->_createInvalidArgumentException($this->__('Invalid path segment separator'), null, null, $separator);
        }

        $this->pathSegmentSeparator = $separator;
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
