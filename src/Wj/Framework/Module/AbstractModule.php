<?php

namespace Wj\Framework\Module;


use Zend\EventManager\EventManager;

use Wj\Framework\Config\ConfigEvents;
use Wj\Framework\Config\ConfigLocator;
use Wj\Framework\Config\Resolver\ModuleNameResolver;


abstract class AbstractModule
{
    private $locator;

    public function getPath()
    {
        $path = explode('\\', get_class($this));
        array_pop($path); // remove classname, we want the directory
        $path = implode('/', $path);

        return 'src/'.$path;
    }

    public function getConfigLocator()
    {
        if (null === $this->locator) {
            $this->setConfigLocator();
        }

        return $this->locator;
    }

    private function setConfigLocator()
    {
        $this->locator = $locator = new ConfigLocator();
        $events = new EventManager();

        $resolver = new ModuleNameResolver();
        $events->attach(ConfigEvents::READ, array($resolver, 'onRead'));

        $locator->setEventManager($events);
    }
}
