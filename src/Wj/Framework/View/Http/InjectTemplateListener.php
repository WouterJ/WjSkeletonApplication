<?php

namespace Wj\Framework\View\Http;


use Zend\Mvc\MvcEvent;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use Zend\Mvc\View\Http\InjectTemplateListener as ZendInjectTemplateListener;


class InjectTemplateListener extends ZendInjectTemplateListener
{
    public function injectTemplate(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $controller = $e->getTarget();
        if (is_object($controller)) {
            $controller = get_class($controller);
        }
        if (!$controller) {
            $controller = $routeMatch->getParam('controller', '');
        }

        parent::injectTemplate($e);
    }

    protected function deriveModuleNamespace($controller)
    {
        return '';
    }
}
