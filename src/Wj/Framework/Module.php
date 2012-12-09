<?php

namespace Wj\Framework;


use Zend\Mvc\MvcEvent;

use Zend\EventManager\StaticEventManager;

use Zend\ModuleManager\Feature\ServiceProviderInterface;

use Wj\Framework\Config\ConfigEvents;
use Wj\Framework\Config\ConfigLocator;
use Wj\Framework\Config\Resolver\ModuleNameResolver;

use Wj\Framework\DependencyInjection\ServiceConfiguration;

use Wj\Framework\View\Http\InjectTemplateListener;


class Module implements ServiceProviderInterface
{
    public function onBootstrap($e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
 
        $this->initDispatcher($serviceManager);
    }

    public function initDispatcher($serviceManager)
    {
        $sharedEvents = $serviceManager->get('SharedEventManager');
 
        $injectTemplateListener = new InjectTemplateListener();
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectTemplateListener, 'injectTemplate'), -81);
    }

    public function getConfig()
    {
        return array();
    }

    public function getServiceConfig()
    {
        return new ServiceConfiguration();
    }
}
