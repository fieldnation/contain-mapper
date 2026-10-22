<?php
namespace ContainMapperTest\Driver\ZendDb;

use ContainTest\Entity\SampleChildEntity;
use ContainTest\Entity\SampleMultiTypeEntity;

class DriverTest extends \PHPUnit\Framework\TestCase
{
    protected $driver;
    protected $entity;

    public function setUp(): void
    {
        if (!class_exists('Zend\Db\Adapter\Adapter')) {
            $this->markTestSkipped(
                'zendframework/zend-db is not installed.'
            );
        }

        $mockConnection = $this->createMock('Zend\Db\Adapter\Driver\ConnectionInterface');
        $mockDriver = $this->createMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));
        $mockAdapter = $this->getMockBuilder('Zend\Db\Adapter\Adapter')
            ->setConstructorArgs(array($mockDriver))
            ->getMock();
        $mockAdapter->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        $connection = new \ContainMapper\Driver\ZendDb\Connection($mockAdapter);
        $this->driver = new \ContainMapper\Driver\ZendDb\Driver($connection, 'test');
        $this->entity = new SampleChildEntity(array('firstName' => 'test'));
    }

    public function testGetUpdateCriteriaUnsetsProperty()
    {
        $entity = new SampleMultiTypeEntity(array('string' => 'test1', 'entity' => $this->entity));
        $entity->clean();
        $entity->clear('string');
        $this->assertEquals(array(), $this->driver->getUpdateCriteria($entity));
    }

    public function testGetInsertCriteriaDoesntIncludeSubDocumentProperty()
    {
        $entity = new SampleMultiTypeEntity(array('string' => 'test1', 'entity' => $this->entity));
        $entity->clean()->getEntity()->clear('firstName')->clean('firstName');
        $criteria = $this->driver->getInsertCriteria($entity);
        $this->assertEquals('test1', $criteria['string']);
        $this->assertEquals(array('string'), array_keys($criteria));
    }

    public function testGetUpdateCriteria()
    {
        $entity1 = new SampleMultiTypeEntity(array('string' => 'test1', 'entity' => $this->entity));
        $this->assertEquals(
            array(
                'string' => 'test1',
            ),
            $this->driver->getUpdateCriteria($entity1)
        );
        $entity1->clean();
        $this->assertEquals(array(), $this->driver->getUpdateCriteria($entity1));
    }

    public function testGetUpdateCriteriaEmptyIfNothingToDo()
    {
        $entity = new SampleMultiTypeEntity(array('string' => 'test1', 'entity' => $this->entity));
        $entity->clean();
        $this->assertEquals(array(), $this->driver->getUpdateCriteria($entity));
    }
}
