<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterContract;

class PdfExporter extends Exporter implements ExporterContract
{
    /**
     * @var string ['A4', 'A3', 'Letter']
     */
    protected $format = 'A4';

    /**
     * @var string ['landscape', 'portrait']
     */
    protected $orientation = 'portrait';


    public function generate()
    {
        // TODO
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return PdfExporter
     */
    public function setFormat($format)
    {
        if (in_array($format, ['A4', 'A3', 'Letter']) ) $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param $orientation
     * @return PdfExporter
     */
    public function setOrientation($orientation){
        if( in_array($orientation, ['landscape', 'portrait']) ) $this->orientation = $orientation;
        return $this;
    }
    
}