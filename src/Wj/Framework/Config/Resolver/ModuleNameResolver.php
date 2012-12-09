<?php

namespace Wj\Framework\Config\Resolver;


class ModuleNameResolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $name A logical module name (e.g. @AcmeAlbum)
     *
     * @return string An absolute path to the module
     */
    public function resolve($name)
    {
        $namespace = $this->getModuleNamespace($name);
        $module = $namespace.'\Module';

        if (class_exists($module)) {
            $reflection = new \ReflectionClass($module);
            if (!$reflection->isSubclassOf('Wj\Framework\Module\AbstractModule')) {
                throw new \LogicException(
                    sprintf(
                        'The Module Logical Name "%s" could not be resolved, the Module class needs to implement Wj\Framework\Module\AbstractModule',
                        $name
                    )
                );
            }

            $module = new $module();

            return $module->getPath();
        } else {
            throw new \RunTimeException(sprintf('The module "%s" does not exists', $module));
        }
    }

    /**
     * Returns the ModuleNamespace from the Module name
     *
     * @param string $name The module name (e.g. AcmeAlbum)
     *
     * @return string The namespace (e.g. Acme\Album)
     */
    public function getModuleNamespace($name)
    {
        $i = 0;
        $namespace = preg_replace_callback('|(?<!\\\)([A-Z])|', function($match) use (&$i) {
            if (3 > ++$i) {
                return '\\'.$match[1];
            } else {
                return $match[1];
            }
        }, $name);

        return $namespace;
    }

    /**
     * This method is called when the ConfigLocator has parsed the configuration file
     *
     * @param \Zend\EventManager\Event $event
     */
    public function onRead($event)
    {
        // get content
        $params = $event->getParams();
        $content = $params['content'];

        // edit content
        $self = $this;
        $content = preg_replace_callback('|@([^/]*)|', function ($match) use ($self) {
            return $self->resolve(trim($match[1]));
        }, $content);

        // save content
        $params['content'] = $content;
    }
}
