<?php

namespace Dhii\Config;

use stdClass;
use Traversable;

/**
 * A factory of config.
 *
 * @since [*next-version*]
 */
class DereferencingConfigMapFactory implements ConfigFactoryInterface
{
    /**
     * Name of the factory config key which stores the data.
     *
     * @since [*next-version*]
     */
    const K_DATA = 'data';

    /**
     * Name of the class that this factory creates.
     *
     * @since [*next-version*]
     */
    const PRODUCT_CLASS_NAME = 'Dhii\Config\DereferencingConfigMap';

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function make($config = null)
    {
        $data = $this->_containerGet($config, static::K_DATA);

        return $this->_make($data);
    }

    /**
     * Creates a config map instance with the given data.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|ArrayObject $data The data for the config.
     *
     * @return ConfigInterface The new config.
     */
    protected function _make($data)
    {
        $className = self::PRODUCT_CLASS_NAME;
        $map = new stdClass();
        $factory = $this->_getFactory();
        foreach ($data as $_key => $_value) {
            if (!is_object($_value) && !is_array($_value)) {
                $map->{$_key} = $_value;
                continue;
            }

            $map->{$_key} = $factory->make((object) [
                self::K_DATA => $_value,
            ]);
        }

        return new $className($map);
    }

    /**
     * Retrieves a factory that makes child elements.
     *
     * @since [*next-version*]
     *
     * @return ConfigFactoryInterface The factory.
     */
    protected function _getFactory()
    {
        return $this;
    }
}
