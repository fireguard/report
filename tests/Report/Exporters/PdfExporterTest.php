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
}