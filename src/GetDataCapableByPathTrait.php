<?php

namespace Dhii\Config;

use ArrayAccess;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * Functionality for retrieving internal data by path.
 *
 * @since [*next-version*]
 */
trait GetDataCapableByPathTrait
{
    /**
     * Retrieves the data associated with the specified key or path.
     *
     * Will traverse the hierarchy of containers identified by the path segments.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $key The key or list of keys in the path to get the data for.
     *
     * @return mixed The data corresponding to the path.
     */
    protected function _getData($key)
    {
        $separator = $this->_getPathSegmentSeparator();
        $path      = $this->_normalizePath($key, $separator);
        $store     = $this->_getDataStore();

        return $this->_containerGetPath($store, $path);
    }

    /**
     * Retrieves the separator of segments in a string path.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable|null The path separator.
     */
    abstract protected function _getPathSegmentSeparator();

    /**
     * Retrieves a value from a chain of nested containers by path.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The top container in the chain to read from.
     * @param array|Traversable|stdClass                        $path      The list of path segments.
     *
     * @throws InvalidArgumentException    If one of the containers in the chain is invalid.
     * @throws ContainerExceptionInterface If an error occurred while reading from one of the containers in the chain.
     * @throws NotFoundExceptionInterface  If one of the containers in the chain does not have the corresponding key.
     *
     * @return mixed The value at the specified path.
     */
    abstract protected function _containerGetPath($container, $path);

    /**
     * Retrieves a pointer to the data store.
     *
     * @since [*next-version*]
     *
     * @return array|ArrayAccess|stdClass|BaseContainerInterface|null The data store.
     */
    abstract protected function _getDataStore();

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
