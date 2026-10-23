<?php
namespace ContainMapperTest\Driver\File;

use ContainMapper\Driver\File;
use ContainTest\Entity\SampleChildEntity;

class DriverTest extends \PHPUnit\Framework\TestCase
{
    protected $driver;
    protected $entity;
    protected $directory;

    public function setUp(): void
    {
        // Isolated directory: find() reads every *.json file, so a shared dir like /tmp leaks unrelated files in.
        $this->directory = sys_get_temp_dir() . '/contain-mapper-file-driver-' . uniqid();
        mkdir($this->directory);

        $connection = new File\Connection($this->directory);
        $this->driver = new File\Driver($connection);
        $this->entity = new SampleChildEntity(array('firstName' => 'test'));
    }

    public function tearDown(): void
    {
        array_map('unlink', glob($this->directory . '/*'));
        rmdir($this->directory);
    }

    public function testPersist()
    {
        $this->assertSame($this->driver, $this->driver->persist($this->entity));
    }

    public function testFindOne()
    {
        $this->driver->persist($this->entity);
        $this->assertEquals($this->entity->export(), (array) $this->driver->findOne('test'));
    }

    public function testFind()
    {
        $this->driver->persist($this->entity);
        $this->assertEquals(array($this->entity->export()), (array) $this->driver->find());
    }

    public function testDelete()
    {
        $this->driver->persist($this->entity);
        $this->driver->delete($this->entity);
        $this->assertFalse($this->driver->findOne('test'));
    }
}
