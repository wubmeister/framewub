<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Entity\AbstractEntity;

class BlockBuilder_Entity_MockEntity extends AbstractEntity
{
    public function __construct(array $definition)
    {
        parent::__construct($definition);
        $this->name = $definition['entity'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMods()
    {
        return $this->mods;
    }
}

class AbstractEntityTest extends TestCase
{
    public function testConstruct()
    {
        $entity = new BlockBuilder_Entity_MockEntity([
            'entity' => 'mock',
            'mods' => [ 'color' => 'blue' ],
            'content' => []
        ]);

        $this->assertEquals('mock', $entity->getName());
        $mods = $entity->getMods();
        $this->assertInternalType('array', $mods);
        $this->assertEquals('blue', $mods['color']);
    }
}
