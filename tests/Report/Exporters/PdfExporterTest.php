<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Report;

class PdfExporterTest extends \PHPUnit_Framework_TestCase
{
    protected $defaultFormat = 'A4';

    protected $defaultOrientation = 'portrait';

    public function testGetDefaultFormat()
    {
        $exporter = new PdfExporter();
        $this->assertEquals($this->defaultFormat, $exporter->getFormat());
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
        $this->assertEquals($this->defaultFormat, $exporter->getFormat());
    }

    public function testGetDefaultOrientation()
    {
        $exporter = new PdfExporter();
        $this->assertEquals($this->defaultOrientation, $exporter->getOrientation());
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
        $this->assertEquals($this->defaultOrientation, $exporter->getOrientation());
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

    public function testAddCommandOption()
    {
        $exporter = new PdfExporter();
        $options = $exporter->getCommandOptions();
        $exporter->addCommandOption('command-option-include', true);
        $this->assertEquals($options, $exporter->getCommandOptions());

        $exporter->addCommandOption('web-security', 'invalid-expected-bool');
        $this->assertEquals($options, $exporter->getCommandOptions());

        $exporter->addCommandOption('disk-cache', true);
        $this->assertArrayHasKey('disk-cache', $exporter->getCommandOptions());
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
        $exporter = new PdfExporter(__DIR__);
        $report = new Report(
            '<section class="content">Content</section>',
            '<section class="header">Header</section>',
            '<section class="footer">Footer</section>'
        );
        $file = $exporter->generate($report);
    }
}