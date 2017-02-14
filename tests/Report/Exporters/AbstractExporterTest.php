<?php
namespace Fireguard\Report\Exporters;

class AbstractExporterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractExporter
     */
    protected $exporter;

    public function setUp()
    {
        $this->exporter = $this->getMockForAbstractClass(AbstractExporter::class);
    }

    public function testExporterConstructor()
    {
        $this->assertFileExists($this->exporter->getPath());
        $this->assertTrue( (
            !file_exists($this->exporter->getFullPath())
            || is_writable($this->exporter->getFullPath())
        ), 'Is not writable path generated');

        $tmpName = str_replace(sys_get_temp_dir(), '', tempnam(sys_get_temp_dir(), 'test-report'));
        $exporter = $this->getMockForAbstractClass(AbstractExporter::class, ['', $tmpName]);
        $this->assertEquals($tmpName, $exporter->getFileName());
    }

    public function testSetPath()
    {
        $exporter = $this->getMockForAbstractClass(AbstractExporter::class);
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.sha1(time()).DIRECTORY_SEPARATOR;
        $exporter->setPath($path);
        $this->assertEquals($path, $exporter->getPath());
    }

    public function testSetFileName()
    {
        $exporter = $this->getMockForAbstractClass(AbstractExporter::class);
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

    public function testSetTimeout()
    {
        $exporter = $this->getMockForAbstractClass(AbstractExporter::class);
        $exporter->setTimeout(100);
        $this->assertEquals(100, $exporter->getTimeout());
    }

    public function testSetConfigDefaultOptions()
    {
        $exporter = new PdfExporter();
        $exporter->setConfigDefaultOptions([]);
        $this->assertEquals([], $exporter->getConfigDefaultOptions());
    }

    public function testSetConfigValidOptions()
    {
        $exporter = new PdfExporter();
        $exporter->setConfigValidOptions([]);
        $this->assertEquals([], $exporter->getConfigValidOptions());
    }

    public function testCompressHtmlAndJsStrings()
    {
        // Test Remove Space
        $html = '<h1>Teste</h1>'.PHP_EOL.'<span>Teste</span>';
        $this->assertEquals('<h1>Teste</h1> <span>Teste</span>', $this->exporter->compress($html));

        // Test Remove Coments
        $comment = '/**'.PHP_EOL.'* Compress html e js removed comments e break lines'.PHP_EOL;
        $comment.= '* @param $buffer'.PHP_EOL.'* @return mixed'.PHP_EOL.'*/ function test()';
        $this->assertEquals('function test()', $this->exporter->compress($comment));
    }

    public function testGetDefaultConfiguration()
    {
        $config = $this->exporter->getDefaultConfiguration();
        $this->assertArrayHasKey('pdf', $config);
        $this->assertArrayHasKey('phantom', $config['pdf']);
        $this->assertArrayHasKey('html', $config);
    }
}
