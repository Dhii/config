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
    const REF_TOKEN_START = '${';

    const REF_TOKEN_END = '}';

    use DereferenceTokensCapableTrait;

    use ReplaceReferencesCapableTrait;

    use GetDataCapableByPathTrait;

    use HasDataCapableByPathTrait;

    use ContainerGetPathCapableTrait;

    use ContainerHasPathCapableTrait;

    use TokenStartAwareTrait;

    use TokenEndAwareTrait;

    use ContainerAwareTrait;

    use PathSegmentSeparatorAwareTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /* @since [*next-version*] */
    use CreateDataStoreCapableTrait;

    use StringableSplitCapableTrait;

    use NormalizePathCapableTrait;

    use NormalizeArrayCapableTrait;

    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    use CreateRuntimeExceptionCapableTrait;

    /**
     * @since [*next-version*]
     *
     * @param ArrayObject|array|stdClass $elements The elements of the map.
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
        }
        catch (RuntimeException $e) {
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
