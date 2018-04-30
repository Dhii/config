<?php

namespace Dhii\Config;

use ArrayObject;
use Dhii\Collection\AbstractBaseMap;
use Dhii\Data\Container\ContainerAwareTrait;
use Dhii\Data\Container\ContainerGetPathCapableTrait;
use Dhii\Data\Container\ContainerHasPathCapableTrait;
use Dhii\Data\Object\CreateDataStoreCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\String\StringableSplitCapableTrait;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Iterator;
use RuntimeException;
use stdClass;

/**
 * A map-based config implementation that can de-reference tokens in config values.
 *
 * @since [*next-version*]
 */
class DereferencingConfigMap extends AbstractBaseMap
{
    /* The delimiter that marks the start of a token.
     *
     * @since [*next-version*]
     */
    const REF_TOKEN_START = '${';

    /* The delimiter that marks the start of a token.
     *
     * @since [*next-version*]
     */
    const REF_TOKEN_END = '}';

    /* @since [*next-version*] */
    use DereferenceTokensCapableTrait;

    /* @since [*next-version*] */
    use ReplaceReferencesCapableTrait;

    /* @since [*next-version*] */
    use GetDataCapableByPathTrait;

    /* @since [*next-version*] */
    use HasDataCapableByPathTrait;

    /* @since [*next-version*] */
    use ContainerGetPathCapableTrait;

    /* @since [*next-version*] */
    use ContainerHasPathCapableTrait;

    /* @since [*next-version*] */
    use TokenStartAwareTrait;

    /* @since [*next-version*] */
    use TokenEndAwareTrait;

    /* @since [*next-version*] */
    use ContainerAwareTrait;

    /* @since [*next-version*] */
    use PathSegmentSeparatorAwareTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /* @since [*next-version*] */
    use CreateDataStoreCapableTrait;

    /* @since [*next-version*] */
    use StringableSplitCapableTrait;

    /* @since [*next-version*] */
    use NormalizePathCapableTrait;

    /* @since [*next-version*] */
    use NormalizeArrayCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateRuntimeExceptionCapableTrait;

    /**
     * @since [*next-version*]
     *
     * @param ArrayObject|array|stdClass $elements The elements of the map.
     * @param BaseContainerInterface The container that will be used for token de-referencing.
     */
    public function __construct($elements, $container = null)
    {
        if (is_array($elements) || ($elements instanceof stdClass)) {
            /* Normalizing to something that is both a writable container,
             * and iterable. This will avoid having to create a new iterator
             * object every time iteration is started, while still avoiding
             * having to copy the elements: `ArrayObject` will work with `stdClass`
             * references as is, and `arrays` enjoy the benefits of copy-on-write.
             */
            $elements = $this->_createDataStore($elements);
        }

        $this->_setDataStore($elements);
        $this->_setTokenStart(static::REF_TOKEN_START);
        $this->_setTokenEnd(static::REF_TOKEN_END);
        $this->_setPathSegmentSeparator(ConfigInterface::KEY_SEPARATOR);

        $container = $container !== null
            ? $container
            : $this;
        $this->_setContainer($container);

        $this->_construct();
    }

    /**
     * {@inheritdoc}
     *
     * Will also dereference tokens in the key.
     *
     * @since [*next-version*]
     */
    public function _get($key)
    {
        $value = parent::_get($key);

        try {
            return $this->_dereferenceTokens($value);
        } catch (RuntimeException $e) {
            throw $this->_createContainerException($this->__('Could not de-reference tokens'), null, $e, $this);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _calculateValue(Iterator $iterator)
    {
        $value = parent::_calculateValue($iterator);

        return $this->_dereferenceTokens($value);
    }
}
