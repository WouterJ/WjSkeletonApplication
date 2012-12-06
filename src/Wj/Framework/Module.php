<?php

namespace Wj\Framework;


use Zend\Mvc\MvcEvent;

use Wj\Framework\Config\ConfigLocator;
use Wj\Framework\View\Http\InjectTemplateListener;


class Module
{
    public function onBootstrap($e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
 
        $this->initModules($serviceManager);
    }

    public function initModules($serviceManager)
    {
        $eventManager = $serviceManager->get('Application')->getEventManager();
        $sharedEvents = $eventManager->getSharedManager();
 
        $injectTemplateListener = new InjectTemplateListener();
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectTemplateListener, 'injectTemplate'), -81);
    }
}
