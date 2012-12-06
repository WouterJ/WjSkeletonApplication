<?php

namespace Wj\Framework\Config;


use Zend\Config\Reader\Ini;
use Zend\Config\Reader\Xml;
use Zend\Config\Reader\Json;

use Symfony\Component\Yaml\Parser;


class ConfigLocator
{
    private $extensions;


    public function __construct($extensions = array())
    {
        // default extensions
        $coreExtensions = array(
            'ini' => function ($filename) {
                $reader = new Ini();
                
                return $reader->fromFile($filename);
            },
            'xml' => function ($filename) {
                $reader = new Xml();
                
                return $reader->fromFile($filename);
            },
            'json' => function ($filename) {
                $reader = new Json();

                return $reader->fromFile($filename);
            },
            'yml' => function ($filename) {
                $parser = new Parser();

                return $parser->parse(file_get_contents($filename));
            },
            'php' => function ($filename) {
                return require $filename;
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

    public function locate($file)
    {
        foreach ($this->getExtensions() as $extension => $factory) {
            if (file_exists($f = $this->getFileName($file, $extension))) {
                $config = $factory($f);

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
