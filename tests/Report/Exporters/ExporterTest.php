<?php
namespace Fireguard\Report\Exporters;

class ExporterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $exporter;

    public function setUp()
    {
        $this->exporter = $this->getMockForAbstractClass(Exporter::class);
    }

    public function testExporterConstructor()
    {

        $this->assertFileExists($this->exporter->getPath());
        $this->assertTrue(is_writable($this->exporter->getFullPath()), 'Is not writable path generated');

        $tmpName = str_replace(sys_get_temp_dir(), '', tempnam(sys_get_temp_dir(), 'test-report'));
        $exporter = $this->getMockForAbstractClass(Exporter::class, ['', $tmpName]);
        $this->assertEquals($tmpName, $exporter->getFileName());
        $this->assertTrue(is_writable($exporter->getFullPath()), 'Is not writable manual path generated');
        unlink($exporter->getFullPath());
    }

    public function testSetPath()
    {
        $exporter = $this->getMockForAbstractClass(Exporter::class);
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.sha1(time()).DIRECTORY_SEPARATOR;
        $exporter->setPath($path);
        $this->assertEquals($path, $exporter->getPath());
        $this->assertTrue(is_writable($exporter->getFullPath()), 'Is not writable manual path generated');
        unlink($exporter->getFullPath());
    }

    public function testSetFileName()
    {
        $exporter = $this->getMockForAbstractClass(Exporter::class);
        $newName = str_replace($exporter->getPath(), '', tempnam($exporter->getPath(), 'test-report'));
        $exporter->setFileName($newName);
        $this->assertEquals($newName, $exporter->getFileName());
        unlink($exporter->getFullPath());
    }

    public function testGetFullPath()
    {
        $path = rtrim( $this->exporter->getPath(), DIRECTORY_SEPARATOR );
        $this->assertEquals($path.DIRECTORY_SEPARATOR.$this->exporter->getFileName(), $this->exporter->getFullPath());
    }
}