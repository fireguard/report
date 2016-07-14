<?php
namespace Fireguard\Report\Exporters;

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
        $expected = '--debug=false --ignore-ssl-errors=true --load-images=true --ssl-protocol=any ';
        $this->assertEquals($expected, $exporter->mountCommandOptions());
    }
}