<?php

namespace Dhii\Config;

use InvalidArgumentException;
use stdClass;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for path normalization.
 *
 * @since [*next-version*]
 */
trait NormalizePathCapableTrait
{
    /**
     * Normalizes a path.
     *
     * Will try to normalize to a list of path segments. If this is not possible, will try to normalize to a string,
     * and the split that string into segments using the specified separator.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable|string|Stringable $path      The path to normalize.
     * @param string|Stringable                            $separator The separator used to split a string path into segments.
     *
     * @return array|stdClass|Traversable The normalized path.
     */
    protected function _normalizePath($path, $separator)
    {
        try {
            return $this->_normalizeIterable($path);
        } catch (InvalidArgumentException $e) {
            return $this->_stringableSplit($path, $separator);
        }
    }

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
    abstract protected function _stringableSplit($subject, $separator);

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);
}
