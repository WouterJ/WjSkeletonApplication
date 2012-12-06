<?php

namespace Wj\Framework\Module;


use Wj\Framework\Config\ConfigLocator;


abstract class AbstractModule
{
    public function getConfig()
    {
        $locator = new ConfigLocator();
        if (method_exists($this, 'getConfigFile')) {
            $config = $locator->locate($this->getConfigFile());
        } else {
            throw new \LogicException(
                'You must override the getConfig() method or create a getConfigFile() method in your Module class.'
            );
        }

        return $config;
    }
}
