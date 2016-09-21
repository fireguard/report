<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Report;

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

    public function testGetDefaultOrientation()
    {
        $exporter = new PdfExporter();
        $this->assertEquals($this->configDefault['page']['orientation'], $exporter->getOrientation());
    }

    public function testSetValidOrientation()
    {
        $exporter = new PdfExporter();
        $exporter->setOrientation('landscape');
        $this->assertEquals('landscape', $exporter->getOrientation());

    }

    public function testSetInvalidOrientation()
    {
        $exporter = new PdfExporter();
        $exporter->setFormat('invalid-orientation');
        $this->assertEquals($this->configDefault['page']['orientation'], $exporter->getOrientation());
    }

    public function testGetMargin()
    {
        $exporter = new PdfExporter();
        $this->assertEquals($this->configDefault['page']['margin'], $exporter->getMargin());
    }

    public function testSetMargin()
    {
        $exporter = new PdfExporter();
        $exporter->setMargin('{top: "0px", right: "0px", bottom: "0px", left: "0px"}');
        $this->assertEquals('{top: "0px", right: "0px", bottom: "0px", left: "0px"}', $exporter->getMargin());

        $exporter->setMargin('5px');
        $this->assertEquals('"5px"', $exporter->getMargin());

    }

    public function testSetCommandOptions()
    {
        $exporter = new PdfExporter();
        $options = [
            'debug' => false,
            'ignore-ssl-errors' => true,
            'load-images' => true,
            'ssl-protocol' => 'any'
        ];
        $exporter->setCommandOptions($options);
        $this->assertEquals($options, $exporter->getCommandOptions());
    }

    public function testSetBinayPath()
    {
        $exporter = new PdfExporter();
        $exporter->setBinaryPath('/path/for/binary');
        $this->assertEquals('/path/for/binary', $exporter->getBinaryPath());
    }

    public function testAddCommandOption()
    {
        $exporter = new PdfExporter();
        $exporter->setConfigValidOptions([
            'web-security' => 'bool',
            'disk-cache' => 'bool',
            'local-storage-path' => 'string',
            'test-option' => 'not-validated-type',
            'ssl-protocol' => [ 'sslv3', 'sslv2', 'tlsv1', 'any']
        ]);

        // Ignore Invalid Option
        $options = $exporter->getCommandOptions();
        $exporter->addCommandOption('command-option-include', true);
        $this->assertEquals($options, $exporter->getCommandOptions());

        // Ignore Invalid Value
        $exporter->addCommandOption('web-security', 'invalid-expected-bool');
        $this->assertEquals($options, $exporter->getCommandOptions());

        // Define Valid Value for Bool
        $exporter->addCommandOption('disk-cache', true);
        $this->assertArrayHasKey('disk-cache', $exporter->getCommandOptions());

        // Define Valid Value for Array
        $exporter->addCommandOption('ssl-protocol', 'any');
        $options = $exporter->getCommandOptions();
        $this->assertArrayHasKey('ssl-protocol', $options);
        $this->assertEquals('any', $options['ssl-protocol']);

        // Define Valid Value for String
        $exporter->addCommandOption('local-storage-path', 'path-string');
        $options = $exporter->getCommandOptions();
        $this->assertArrayHasKey('local-storage-path', $options);
        $this->assertEquals('path-string', $options['local-storage-path']);

        // Define Valid Value for not validated format
        $exporter->addCommandOption('test-option', 'any-value');
        $options = $exporter->getCommandOptions();
        $this->assertArrayHasKey('test-option', $options);
        $this->assertEquals('any-value', $options['test-option']);
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

//    public function testMountScriptForExport()
//    {
//        $exporter = new PdfExporter();
//        $file = $exporter->mountScriptForExport();
//        $expected = 'var fs = require("fs"),args = require("system").args,page = require("webpage").create(); page.content = fs.read(args[1]);page.viewportSize = {width: 600, height: 600};page.paperSize = {format: "'.$exporter->getFormat().'",orientation: "'.$exporter->getOrientation().'",margin: "1cm",footer: {height: "1cm",contents: phantom.callback(function (pageNum, numPages) {return "<div style=\'text-align: right; font-size: 12px;\'>" + pageNum + " / " + numPages + "</div>";})}}; window.setTimeout(function() {page.render(args[1]);phantom.exit();}, 250);';
//        $this->assertStringEqualsFile($file, $expected);
//    }


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

    public function testPrefixerFilePath()
    {
        $exporter = new PdfExporter();

        $this->assertEquals('/var/www', $exporter->prefixOsPath('/var/www'));

        $this->assertEquals('/var/www', $exporter->prefixOsPath('/var/www'), 'LINUX');

        $this->assertEquals('file:///c:/www', $exporter->prefixOsPath('c:/www', 'WIN'));
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