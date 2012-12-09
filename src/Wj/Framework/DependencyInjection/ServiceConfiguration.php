<?php

namespace Wj\Framework\DependencyInjection;


use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

use Zend\EventManager\EventManager;

use Wj\Framework\Config\ConfigEvents;
use Wj\Framework\Config\ConfigLocator;
use Wj\Framework\Config\Resolver\ModuleNameResolver;


class ServiceConfiguration extends Config
{
    public function getFactories()
    {
        return array(
            'wjconfig.eventmanager.events' => function ($sm) {
                return array(
                    ConfigEvents::READ => array(
                        array(new ModuleNameResolver(), 'onRead'),
                    ),
                );
            },
            'wjconfig.eventmanager' => function ($sm) {
                $events = new EventManager();

                foreach ($sm->get('wj_config.eventmanager.events') as $eventname => $event) {
                    foreach ($event as $e) {
                        $events->attach($eventname, $e);
                    }
                }

                return $events;
            },

            'wjconfig.locator' => function ($sm) {
                $config = new ConfigLocator();
                $config->setEventManager($sm->get('wj_config.eventmanager'));

                return $config;
            },
        );
    }
}
