<?php

namespace Wj\Framework\Module;


use Wj\Framework\Config\ConfigLocator;


abstract class AbstractModule
{
    private $locator;

    public function getConfigLocator()
    {
        if (null === $this->locator) {
            $this->setConfigLocator();
        }

        return $this->locator;
    }

    private function setConfigLocator()
    {
        $this->locator = new ConfigLocator();
    }
}
