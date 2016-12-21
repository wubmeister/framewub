<?php

use PHPUnit\Framework\TestCase;

use Framewub\ServiceFactory;

class MockService
{
}

class RegMockService
{
}

class ServiceFactoryTest extends TestCase
{
    public function testGet()
    {
        $service = ServiceFactory::get(MockService::class);
        $this->assertInstanceOf(MockService::class, $service);
    }

    public function testSingleton()
    {
        $service = ServiceFactory::get(MockService::class);
        $service2 = ServiceFactory::get(MockService::class);
        $this->assertTrue($service === $service2);
    }

    public function testRegister()
    {
        ServiceFactory::register('Mock', function () { return new RegMockService(); });
        $service = ServiceFactory::get('Mock');
        $this->assertInstanceOf(RegMockService::class, $service);
    }
}
