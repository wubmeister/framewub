<?php

use PHPUnit\Framework\TestCase;

use Framewub\Container;
use Framewub\Config;

class Container_MockService {}
class Container_MockService2 {}
class Container_MockFactory
{
    public function __invoke(Container $container, string $name)
    {
        return new Container_MockService2();
    }
}

class ContainerTest extends TestCase
{
    private $config;

    public function setUp()
    {
        $this->config = new Config([
            'factories' => [
                'MyFactory' => function (Container $container, string $name) {
                    return new Container_MockService();
                },
                'MyClassFactory' => Container_MockFactory::class
            ],
            'invokables' => [
                Container_MockService::class => Container_MockService::class,
                Container_MockService2::class => Container_MockService2::class,
            ]
        ]);
    }

    public function testHas()
    {
        $container = new Container($this->config);
        $this->assertFalse($container->has('DummyFactory'));
        $this->assertTrue($container->has('MyFactory'));
    }

    public function testFactory()
    {
        $container = new Container($this->config);

        $service = $container->get('MyFactory');
        $this->assertInstanceOf(Container_MockService::class, $service);

        $service = $container->get('MyClassFactory');
        $this->assertInstanceOf(Container_MockService2::class, $service);
    }

    public function testInvokable()
    {
        $container = new Container($this->config);

        $service = $container->get(Container_MockService::class);
        $this->assertInstanceOf(Container_MockService::class, $service);

        $service = $container->get(Container_MockService2::class);
        $this->assertInstanceOf(Container_MockService2::class, $service);
    }

    public function testSet()
    {
        $container = new Container($this->config);

        $service = new Container_MockService();
        $service2 = new Container_MockService2();
        $container->set('MockService', $service);
        $container->set('MockService2', $service2);

        $this->assertEquals($service, $container->get('MockService'));
        $this->assertEquals($service2, $container->get('MockService2'));
    }
}
