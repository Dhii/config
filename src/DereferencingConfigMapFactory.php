<?php

namespace Dhii\Config;

use stdClass;
use Traversable;

class DereferencingConfigMapFactory implements ConfigFactoryInterface
{
    const K_DATA = 'data';

    const PRODUCT_CLASS_NAME = 'Dhii\Config\DereferencingConfigMap';

    public function make($config = null)
    {
        $data = $config->{static::K_DATA};

        return $this->_make($data);
    }

    /**
     * Creates a config map instance with the given data.
     *
     * @since [*next-version*]
     *
     * @param object|stdClass|Traversable $data The
     *
     * @return ConfigMap The new config map.
     */
    protected function _make($data)
    {
        $className = self::PRODUCT_CLASS_NAME;
        $map = new stdClass();
        foreach ($data as $_key => $_value) {
            if (!is_object($_value) && !is_array($_value)) {
                $map->{$_key} = $_value;
                continue;
            }

            $map->{$_key} = $this->_getFactory()->make((object) [self::K_DATA => $_value]);
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
