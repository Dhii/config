<?php

namespace Dhii\Config;

use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;

trait GetPathSegmentSeparatorCapableStandardTrait
{
    /**
     * Retrieves the separator for the specified key.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $key The key to get the path separator for, if any.
     *
     * @throws InvalidArgumentException If key is invalid.
     *
     * @return string|Stringable The path separator.
     *                           The separator may be deduced from the key, but this is NOT REQUIRED.
     *                           If no key is supplied, a default separator MUST be returned.
     */
    protected function _getPathSegmentSeparator($key = null)
    {
        return ConfigInterface::KEY_SEPARATOR;
    }
}
