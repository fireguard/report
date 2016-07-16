<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterContract;
use Fireguard\Report\Contracts\ReportContract;
use Fireguard\Report\ReportGenerator;
use PhantomInstaller\PhantomBinary;
use RuntimeException;
use Symfony\Component\Process\Process;

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

    /**
     * @var array PhantomJs Params
     */
    protected $commandOptions = [];


    public function initialize()
    {
        $this->extension = '.pdf';

        $options = include __DIR__.'/../../../config/phantom.php';
        $this->setConfigDefaultOptions($options['defaultOptions']);
        $this->setConfigValidOptions($options['validOptions']);

        $this->commandOptions = $this->configDefaultOptions;
        $this->setBinaryPath(PhantomBinary::getBin());
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
        $exporter = new HtmlExporter($this->getPath(), $this->fileName);
        $this->htmlBodyPath = $exporter->saveFile($report->getContent());

        if ($header = empty($report->getHeader())) {
            $exporter->setFileName($this->fileName.'-header');
            $this->htmlHeaderPath = $exporter->saveFile($header);
        }

        if ($footer = empty($report->getFooter())) {
            $exporter->setFileName($this->fileName.'-footer');
            $this->htmlFooterPath = $exporter->saveFile($footer);
        }
    }

    protected function savePdfFile()
    {
        $command = implode(' ', [
            $this->binaryPath,
            $this->mountCommandOptions(),
            $this->mountScriptForExport(),
            $this->prefixOsPath($this->htmlBodyPath),
            $this->getFullPath()
        ]);

        $process = new Process($command, $this->getPath());
        $process->setTimeout($this->timeout);
        $process->run();

        if ($errorOutput = $process->getErrorOutput()) {
            throw new RuntimeException('PhantomJS: ' . $errorOutput);
        }

        // Remove temporary html file
        if ($this->htmlHeaderPath) @unlink($this->htmlHeaderPath);
        if ($this->htmlFooterPath) @unlink($this->htmlFooterPath);
        @unlink($this->htmlBodyPath);

        return $this->getFullPath();
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
     * @return string
     */
    public function getBinaryPath()
    {
        return $this->binaryPath;
    }

    /**
     * @param $binaryPath
     * @return PdfExporter
     */
    public function setBinaryPath($binaryPath){
        $this->binaryPath = $binaryPath;
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
        if ( isset($this->configValidOptions[$option])) {
            $type = $this->configValidOptions[$option];
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
     * Prefix the input path for windows versions of PhantomJS
     * @param string $path
     * @param string $os
     * @return string
     */
    public function prefixOsPath($path, $os = PHP_OS)
    {
        if (strtoupper(substr($os, 0, 3)) === 'WIN') {
            return 'file:///' . str_replace('\\', '/', $path);
        }

        return $path;
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

        return rtrim($options, ' ');
    }

    /**
     * @return string Path for generated script
     */
    public function mountScriptForExport()
    {
        $script = ' 
            var fs = require("fs");
            var args = require("system").args;
            var page = require("webpage").create();
            
            page.viewportSize = {width: 1024, height: 768};
            
            page.paperSize = {
                format: "'.$this->format.'",
                orientation: "'.$this->orientation.'",
                margin: "1cm",
                footer: {
                    height: "1cm"
                }
            };
            
            page.open( args[1], function( status ) {
                console.log( "Status: " + status );

                if ( status === "success" ) {
                    page.render( args[2] );
                }

                phantom.exit();
            });
            
        ';
        $filePath = tempnam(sys_get_temp_dir(), 'report-script-');
        file_put_contents($filePath, $this->compress($script));
        return $filePath;
    }

}