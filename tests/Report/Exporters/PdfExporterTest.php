<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Report;
use Symfony\Component\HttpFoundation\Response;

class PdfExporterTest extends \PHPUnit_Framework_TestCase
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
        $this->exporter = new PdfExporter();
        $tmpConfig = $this->exporter->getDefaultConfiguration();
        $this->configDefault = $tmpConfig['pdf'];
    }

    public function testGetDefaultFormat()
    {
        $exporter = new PdfExporter();
        $this->assertEquals($this->configDefault['page']['format'], $exporter->getFormat());
    }

    public function testSetValidFormat()
    {
        $exporter = new PdfExporter();
        $exporter->setFormat('A3');
        $this->assertEquals('A3', $exporter->getFormat());

    }

    public function testSetInvalidFormat()
    {
        $exporter = new PdfExporter();
        $exporter->setFormat('invalid-format');
        $this->assertEquals($this->configDefault['page']['format'], $exporter->getFormat());
    }



    public function testMountCommandLine()
    {
        $exporter = new PdfExporter();
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

    public function testGeneratePdf()
    {
        $exporter = new PdfExporter();
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
    public function testGeneratePdfExpectedException()
    {
        $exporter = new PdfExporter();
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
        $exporter = new PdfExporter();
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
        $exporter = new PdfExporter();
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
        $exporter = new PdfExporter(null, 'test-file-name-inline');
        $report = new Report('<section class="content">Content</section>');
        $response = $exporter->response($report);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertFalse(
            $response->headers->contains('content-disposition', 'attachment; filename="test-file-name-inline.pdf"'),
            'Could not find header to force download'
        );
    }

    public function testCreateResponseForceDownload()
    {
        $exporter = new PdfExporter(null, 'test-file-name-download');
        $report = new Report('<section class="content">Content</section>');
        $response = $exporter->response($report, true);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertTrue(
            $response->headers->contains('content-disposition', 'attachment; filename="test-file-name-download.pdf"'),
            'Could not find header to force download'
        );
    }

    public function testGetFooterHeight()
    {
        $exporter = new PdfExporter();
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
