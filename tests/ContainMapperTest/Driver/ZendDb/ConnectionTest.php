<?php
namespace ContainMapperTest\Driver\ZendDb;

use ContainMapper\Driver\ZendDb;

class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    protected $adapter;

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
        $this->adapter = $this->getMockBuilder('Zend\Db\Adapter\Adapter')
            ->setConstructorArgs(array($mockDriver))
            ->getMock();
    }

    public function testConstruct()
    {
        $connection = new ZendDb\Connection($this->adapter);
        $this->assertInstanceOf('Zend\Db\Adapter\Adapter', $connection->getConnection());
    }
}
