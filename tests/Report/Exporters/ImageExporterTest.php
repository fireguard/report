<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Report;
use Symfony\Component\HttpFoundation\Response;

class ImageExporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PdfExporter
     */
    protected $exporter;

    /**
     * @var array
     */
    protected $configDefault;

    public function setUp()
    {
        parent::setUp();
        $this->exporter = new ImageExporter();
        $tmpConfig = $this->exporter->getDefaultConfiguration();
        $this->configDefault = $tmpConfig['image'];
    }

    public function testGetDefaultFormat()
    {
        $exporter = new ImageExporter();
        $this->assertEquals($this->configDefault['page']['format'], $exporter->getFormat());
    }

    public function testSetValidFormat()
    {
        $exporter = new ImageExporter();
        $exporter->setFormat('PNG');
        $this->assertEquals('PNG', $exporter->getFormat());
    }

    public function testSetInvalidFormat()
    {
        $exporter = new ImageExporter();
        $exporter->setFormat('invalid-format');
        $this->assertEquals($this->configDefault['page']['format'], $exporter->getFormat());
    }

    public function testMountCommandLine()
    {
        $exporter = new ImageExporter();
        $options = [
            'debug' => false,
            'ignore-ssl-errors' => true,
            'load-images' => true,
            'ssl-protocol' => 'any'
        ];
        $exporter->setCommandOptions($options);
        $expected = '--debug=false --ignore-ssl-errors=true --load-images=true --ssl-protocol=any';
        $this->assertEquals($expected, $exporter->mountCommandOptions());
    }

    public function testGenerateImage()
    {
        $exporter = new ImageExporter();
        $report = new Report(
            '<section class="content">Content</section>',
            '<section class="header">Header</section>',
            '<section class="footer">Footer</section>'
        );
        $file = $exporter->generate($report);
        $this->assertFileExists($file);
        $this->assertTrue( filesize($file) > 1000 , 'Generate file is empty');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGenerateImageExpectedException()
    {
        $exporter = new ImageExporter();
        $report = new Report(
            '<section class="content">Content</section>',
            '<section class="header">Header</section>',
            '<section class="footer">Footer</section>'
        );
        $exporter->setBinaryPath('invalid-path');
        $exporter->generate($report);
    }


    public function testGeneratePdfOnlyHeader()
    {
        $exporter = new ImageExporter();
        $report = new Report(
            '<section class="content">Content</section>',
            '<section class="header">Header</section>'
        );
        $file = $exporter->generate($report);
        $this->assertFileExists($file);
        $this->assertTrue( filesize($file) > 1000 , 'Generate file is empty');
    }

    public function testGeneratePdfOnlyFooter()
    {
        $exporter = new ImageExporter();
        $report = new Report(
            '<section class="content">Content</section>',
            '',
            '<section class="header">Footer</section>'
        );
        $file = $exporter->generate($report);
        $this->assertFileExists($file);
        $this->assertTrue( filesize($file) > 1000 , 'Generate file is empty');
    }

    public function testCreateResponseInline()
    {
        $exporter = new ImageExporter(null, 'test-file-name-inline');
        $report = new Report('<section class="content">Content</section>');
        $response = $exporter->setFormat('JPG')->response($report);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertFalse(
            $response->headers->contains('content-disposition', 'attachment; filename="test-file-name-inline.jpg"'),
            'Could not find header to force download'
        );
    }

    public function testCreateResponseForceDownload()
    {
        $exporter = new ImageExporter(null, 'test-file-name-download');
        $report = new Report('<section class="content">Content</section>');
        $response = $exporter->setFormat('JPG')->response($report, true);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertTrue(
            $response->headers->contains('content-disposition', 'attachment; filename="test-file-name-download.jpg"'),
            'Could not find header to force download'
        );
    }

    public function testGetFooterHeight()
    {
        $exporter = new ImageExporter();
        $exporter->configure(['footer' => ['height' => '0px']]);
        $this->assertEquals('0px', $exporter->getFooterHeight());

        $exporter->configure(['footer' => ['height' => '100px']]);
        $this->assertEquals('100px', $exporter->getFooterHeight());
    }

    public function testGetHeaderHeight()
    {
        $exporter = new PdfExporter();
        $exporter->configure(['header' => ['height' => '0px']]);
        $this->assertEquals('0px', $exporter->getHeaderHeight());

        $exporter->configure(['header' => ['height' => '100px']]);
        $this->assertEquals('100px', $exporter->getHeaderHeight());
    }
}
