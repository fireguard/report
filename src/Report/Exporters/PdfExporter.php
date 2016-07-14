<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterContract;
use Fireguard\Report\Contracts\ReportContract;
use Fireguard\Report\ReportGenerator;

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

    /**
     * @var string Path for executable converter html to pdf
     */
    protected $binaryPath;

    protected $htmlBodyPath = false;

    protected $htmlHeaderPath = false;

    protected $htmlFooterPath = false;

    protected $configOptions = [];

    /**
     * @var array PhantomJs Params
     */
    protected $commandOptions = [];


    public function initialize()
    {
        $this->extension = '.pdf';
        $this->configOptions = include __DIR__.'/../../../config/phantom.php';
        $this->commandOptions = $this->configOptions['defaultOptions'];
    }

    /**
     * @param ReportContract $report
     * @return string
     */
    public function generate(ReportContract $report)
    {
        $this->createHtmlFiles($report);
        return $this->savePdfFile();
    }

    protected function createHtmlFiles(ReportContract $report)
    {
        $exporter = new HtmlExporter($this->getPath(), $this->fileName.'.html');
        $this->htmlBodyPath = $exporter->saveFile($report->getContent());

        if ($header = empty($report->getHeader())) {
            $exporter->setFileName($this->fileName.'-header.html');
            $this->htmlHeaderPath = $exporter->saveFile($header);
        }

        if ($footer = empty($report->getFooter())) {
            $exporter->setFileName($this->fileName.'-footer.html');
            $this->htmlFooterPath = $exporter->saveFile($footer);
        }
    }

    protected function savePdfFile()
    {
        return false;
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

    /**
     * @return array
     */
    public function getCommandOptions()
    {
        return $this->commandOptions;
    }

    /**
     * @param array $options
     * @return PdfExporter
     */
    public function setCommandOptions($options)
    {
        $this->commandOptions = $options;
        return $this;
    }

    /**
     * @param string $option
     * @return PdfExporter
     */
    public function addCommandOption($option, $value)
    {
        if ( array_key_exists($option, $this->configOptions['validOptions']) ) {
            $type = $this->configOptions['validOptions'][$option];
            if (is_array($type)) {
                if (in_array($value, $type)) $this->commandOptions[$option] = $value;
            }
            else {
                switch ($type) {
                    case 'string' :
                        if (!empty($value)) $this->commandOptions[$option] = $value;
                        break;
                    case 'bool' :
                        if (is_bool($value)) $this->commandOptions[$option] = $value;
                        break;
                    default:
                        $this->commandOptions[$option] = $value;
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * @return string Command line string
     */
    public function mountCommandOptions()
    {
        $options = '';
        foreach ($this->commandOptions as $key => $value) {
            if ( is_bool($value) ) $value = ($value) ? 'true' : 'false';
            $options .= '--'.$key.'='.$value.' ';
        }

        return $options;
    }

}