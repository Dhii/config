<?php

namespace Dhii\Config;

use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * Functionality for splitting a string into pieces.
 *
 * @since [*next-version*]
 */
trait StringSplitCapableTrait
{
    /**
     * Splits a string into pieces using a separator.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $subject   The string to split.
     * @param string|Stringable $separator The separator to split by.
     *
     * @throws InvalidArgumentException If the subject or the separator are invalid.
     *
     * @return array|stdClass|Traversable The list of pieces.
     */
    protected function _stringSplit($subject, $separator)
    {
        $subject   = $this->_normalizeString($subject);
        $separator = $this->_normalizeString($separator);

        return explode($separator, $subject);
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
