<?php

namespace Dhii\Config;

use ArrayAccess;
use ArrayObject;
use Dhii\Collection\AbstractRecursiveMapFactory;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\CreateNotFoundExceptionCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use InvalidArgumentException;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Data\Container\ContainerAwareTrait;
use RuntimeException;
use Exception as RootException;
use stdClass;

/**
 * A factory of config.
 *
 * @since [*next-version*]
 */
class DereferencingConfigMapFactory extends AbstractRecursiveMapFactory implements ConfigFactoryInterface
{
    /* @since [*next-version*] */
    use ContainerAwareTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /* @since [*next-version*] */
    use ContainerGetCapableTrait;

    /* @since [*next-version*] */
    use ContainerHasCapableTrait;

    /* @since [*next-version*] */
    use NormalizeKeyCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateContainerExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateNotFoundExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateRuntimeExceptionCapableTrait;

    /**
     * Name of the factory config key which stores the parent container for token de-referencing.
     *
     * @since [*next-version*]
     */
    const K_REFERENCE_CONTAINER = 'reference_container';

    /**
     * Name of the class that this factory creates.
     *
     * @since [*next-version*]
     */
    const PRODUCT_CLASS_NAME = 'Dhii\Config\DereferencingConfigMap';

    public function __construct(BaseContainerInterface $container = null)
    {
        $this->_setContainer($container);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getChildConfig($child, $config)
    {
        return [
            ConfigFactoryInterface::K_DATA => $child,
            static::K_REFERENCE_CONTAINER  => $this->_getReferenceContainer($config),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _normalizeScalarChild($child, $config)
    {
        return $child;
    }

    /**
     * Creates a new factory product.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The data for the new product instance.
     * @param array|stdClass|ArrayObject                             $data   The data for the new product instance.
     *
     * @throws InvalidArgumentException If the data or the config is invalid.
     * @throws RuntimeException         If the product could not be created.
     *
     * @return mixed The new factory product.
     */
    protected function _createProduct($config, $data)
    {
        $className          = static::PRODUCT_CLASS_NAME;
        $referenceContainer = $this->_getReferenceContainer($config);

        return new $className($data, $referenceContainer);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getChildFactory($child, $config)
    {
        return $this;
    }

    /**
     * Retrieves the reference container to create the child with.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The config of the product, the child of which to get the container for.
     *
     * @throws RuntimeException If the reference container could not be retrieved.
     *
     * @return BaseContainerInterface|null The reference container, if any.
     */
    protected function _getReferenceContainer($config)
    {
        try {
            $container = $this->_containerHas($config, static::K_REFERENCE_CONTAINER)
                ? $this->_containerGet($config, static::K_REFERENCE_CONTAINER)
                : $this->_getContainer();
        } catch (RootException $e) {
            throw $this->_createRuntimeException('Could not retrieve a valid referencing container');
        }

        return $container;
    }
}
