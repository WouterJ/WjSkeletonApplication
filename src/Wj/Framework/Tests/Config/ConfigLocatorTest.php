<?php

namespace Wj\Framework\Tests\Config;


use Zend\EventManager\EventManager;
use Zend\EventManager\StaticEventManager;

use Wj\Framework\Config\ConfigEvents;
use Wj\Framework\Config\ConfigLocator;


class ConfigLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getDefaultExtensionsData
     */
    public function testDefaultExtensions($extension)
    {
        $locator = new ConfigLocator();
        $this->assertArrayHasKey($extension, $locator->getExtensions(), sprintf('The locator supports the "%s" extension', $extension));
    }

    public function getDefaultExtensionsData()
    {
        $extensions = array('xml', 'php', 'yml', 'json', 'ini');

        return array_map(function ($i) {
            return array($i);
        }, $extensions);
    }

    public function testParser()
    {
        $locator = new ConfigLocator();
        $config = $locator->locate(__DIR__.'/../Stubs/config/config');

        $this->assertEquals(array('foo' => array('bar' => 'Cat')), $config);
    }

    public function testParserWithEvents()
    {
        $called = false;

        $em = new EventManager();
        $em->attach(ConfigEvents::PARSE, function ($e) use (&$called) {
            $params = $e->getParams();
            $params['foo'] = 'bar';

            $called = true;
        });

        $locator = new ConfigLocator();
        $locator->setEventManager($em);

        $locator->locate(__DIR__.'/../Stubs/config/config');

        $this->assertTrue($called, 'The PARSE event is triggered');
    }
}
