<?php

namespace Wj\Framework\Tests\Config\Resolver;

use Wj\Framework\Config\Resolver\ModuleNameResolver;

class ModuleNameResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getCorrectNamespaceData
     */
    public function testGetCorrectNamespace($input, $expect)
    {
        $resolver = new ModuleNameResolver();

        $this->assertEquals($expect, $resolver->getModuleNamespace($input));
    }

    public function getCorrectNamespaceData()
    {
        return array(
            array('AcmeAlbum', '\Acme\Album'),
            array('Acme\ModuleAlbum', '\Acme\Module\Album'),
            array('AcmeAdminGenerator', '\Acme\AdminGenerator'),
        );
    }

    /**
     * @expectedException \RunTimeException
     */
    public function testThrowExceptionIfModuleDoesNotExists()
    {
        $resolver = new ModuleNameResolver();

        $resolver->resolve('Acme\AlbumFoo');
    }

    /**
     * @expectedException \LogicException
     */
    public function testThrowExceptionIfModuleDoesNotImplementAbstractModule()
    {
        $resolver = new ModuleNameResolver();

        $resolver->resolve('Wj\Framework\TestsStubs');
    }

    public function testFullConfigurationFile()
    {
        $resolver = new ModuleNameResolver();

        $params = array(
            'content' => '
                foo:
                    bar: @Wj\Framework\Tests\StubsCorrect
            ',
        );
        $expected = array(
            'content' => '
                foo:
                    bar: src/Wj/Framework/Tests/Stubs/Correct
            ',
        );
        $resolver->onRead($this->getEvent($params));

        $this->assertEquals($expected, $params);
    }

    protected function getEvent($params)
    {
        if (!$params instanceof \ArrayObject) {
            $params = new \ArrayObject($params);
        }

        $event = $this->getMock('Zend\EventManager\EventInterface');
        $event
            ->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue($params))
        ;

        return $event;
    }
}
