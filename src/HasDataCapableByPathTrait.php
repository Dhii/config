<?php

namespace Dhii\Config;

use ArrayAccess;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
     * Functionality for checking for data by path.
     *
     * @since [*next-version*]
     */
    trait HasDataCapableByPathTrait
    {
        public function _hasData($key)
        {
            $separator = $this->_getPathSegmentSeparator($key);
            $path      = $this->_normalizePath($key, $separator);
            $store     = $this->_getDataStore();

            return $this->_containerHasPath($store, $path);
        }

    /**
     * Check that path exists on a chain of nested containers.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The container to read from.
     * @param array|Traversable|stdClass                        $path      The key of the value to retrieve.
     *
     * @throws ContainerExceptionInterface If an error occurred while reading from the container.
     * @throws OutOfRangeException         If the container or the key is invalid.
     * @throws NotFoundExceptionInterface  If the key was not found in the container.
     *
     * @return bool True if the container has an entry for the given key, false if not.
     */
    abstract protected function _containerHasPath($container, $path);

    /**
     * Retrieves a pointer to the data store.
     *
     * @since [*next-version*]
     *
     * @return array|ArrayAccess|stdClass|BaseContainerInterface|null The data store.
     */
    abstract protected function _getDataStore();

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
    abstract protected function _getPathSegmentSeparator($key = null);

    /**
     * Normalizes a path.
     *
     * Will try to normalize to a list of path segments. If this is not possible, will try to normalize to a string,
     * and the split that string into segments using the specified separator.
     *
     * @since [*next-version*]
     *
     * @param mixed $path The path to normalize.
     * @param $separator
     *
     * @return array|stdClass|Traversable The normalized path.
     */
    abstract protected function _normalizePath($path, $separator);
    }
