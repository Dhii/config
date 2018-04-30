<?php

namespace Dhii\Config;

use ArrayAccess;
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
            $separator = $this->_getPathSegmentSeparator();
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
     * Retrieves the separator of segments in a string path.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable|null The path separator.
     */
    abstract protected function _getPathSegmentSeparator();

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
