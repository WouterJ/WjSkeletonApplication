<?php

namespace Wj\Framework\View\Http;


use Zend\Mvc\MvcEvent;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use Zend\Mvc\View\Http\InjectTemplateListener as ZendInjectTemplateListener;


class InjectTemplateListener extends ZendInjectTemplateListener
{
    private $isVendor = false;

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
        $moduleNamespace = substr($controller, 0, strpos($controller, '\Controller'));
        $module = $moduleNamespace.'\Module';

        if (class_exists($module)) {
            $moduleReflection = new \ReflectionClass($module);
            if ($moduleReflection->isSubclassOf('Wj\Framework\Module\AbstractModule')) {
                $this->isVendor = true;
            }
        }

        parent::injectTemplate($e);
    }

    protected function deriveModuleNamespace($controller)
    {
        if (!$this->isVendor) {
            return parent::deriveModuleNamespace($controller);
        }
        return '';
    }
}
