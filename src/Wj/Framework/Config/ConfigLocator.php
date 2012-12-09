<?php

namespace Wj\Framework\Config;


use Zend\Config\Reader\Ini;
use Zend\Config\Reader\Xml;
use Zend\Config\Reader\Json;

use Zend\EventManager\EventManager;
use Zend\EventManager\StaticEventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

use Symfony\Component\Yaml\Parser;


class ConfigLocator implements EventManagerAwareInterface
{
    private $extensions;
    private $event_manager;


    public function __construct($extensions = array())
    {
        // default extensions
        $coreExtensions = array(
            'ini' => function ($content) {
                $reader = new Ini();
                
                return $reader->fromString($content);
            },
            'xml' => function ($content) {
                $reader = new Xml();
                
                return $reader->fromString($content);
            },
            'json' => function ($content) {
                $reader = new Json();

                return $reader->fromString($content);
            },
            'yml' => function ($content) {
                $parser = new Parser();

                return $parser->parse($content);
            },
            'php' => function ($content) {
                // whoea, this is really bad (todo)
                return eval($content);
            },
        );
        foreach ($coreExtensions as $extension => $factory) {
            $this->setExtension($extension, $factory);
        }

        // user defined extensions
        foreach ($extensions as $extension => $factory) {
            $this->setExtension($extension, $factory);
        }
    }

    public function setExtension($extension, $factory)
    {
        $this->extensions[$extension] = $factory;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->event_manager = $eventManager;
        $this->event_manager->setSharedManager(StaticEventManager::getInstance());
    }

    public function getEventManager()
    {
        if (null === $this->event_manager) {
            $this->setEventManager(new EventManager('Resolver'));
        }

        return $this->event_manager;
    }

    public function locate($file)
    {
        $eventManager = $this->getEventManager();

        foreach ($this->getExtensions() as $extension => $factory) {
            if (file_exists($f = $this->getFileName($file, $extension))) {
                $content = file_get_contents($f);
                $parameters = $eventManager->prepareArgs(array('content' => $content));
                $this->getEventManager()->trigger(ConfigEvents::READ, 'Resolver', $parameters);
                $parameters = (array) $parameters;
                $content = $parameters['content'];

                $config = $factory($content);

                break;
            }
        }
        if (!isset($config)) {
            throw new \LogicException(
                sprintf(
                    'The configuration file ("%s") is not found',
                    $file
                )
            );
        }

        $parameters = $eventManager->prepareArgs($config);
        $eventManager->trigger(ConfigEvents::PARSE, 'Resolver', $parameters);
        $config = (array) $parameters;

        return $config;
    }

    private function getFileName($file, $extension)
    {
        $data = explode('.', $file);
        $ext = end($data);

        if ($extension == $ext) {
            return $file;
        }

        return $file.'.'.$extension;
    }
}
