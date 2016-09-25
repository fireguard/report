<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterContract;
use Fireguard\Report\Contracts\ReportContract;
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

    protected $htmlHeader = '';

    protected $htmlFooter = '';

    /**
     * @var array PhantomJs Params
     */
    protected $commandOptions = [];

    protected $configValidOptions = [
        'debug' => 'bool',
        'cookies-file' => 'string',
        'disk-cache' => 'bool',
        'load-images' => 'bool',
        'local-storage-path' => 'string',
        'local-storage-quota' => 'integer',
        'local-to-remote-url-access' => 'bool',
        'max-disk-cache-size' => 'integer', //in KB
        'output-encoding' => 'string',
        'proxy' => 'string', //192.168.1.42:8080
        'proxy-type' => ['http', 'socks5', 'none'],
        'proxy-auth' => 'string', //username:password
        'script-encoding' => 'script',
        'ssl-protocol' => [ 'sslv3', 'sslv2', 'tlsv1', 'any'],
        'ssl-certificates-path' => 'string',
        'web-security' => 'bool',
        'webdriver' => 'string',
        'webdriver-selenium-grid-hub' => 'string'
    ];

    /**
     * @param array $config
     * @return ExporterContract
     */
    public function configure(array $config = [])
    {
        $this->extension = '.pdf';
        $defaultConfig = $this->getDefaultConfiguration();
        $this->config = array_replace_recursive($defaultConfig['pdf'] , $config);

//        if(!empty($config)) { var_dump($this->config);exit(); }
        $this->setConfigDefaultOptions($this->config['phantom']);

        $this->commandOptions = $this->configDefaultOptions;
        $this->setBinaryPath((new PhantomBinary)->getBin());

        return $this;
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

        $this->htmlHeader = $this->processInlineHtml($report->getHeader());

        $this->htmlFooter = $this->processInlineHtml($report->getFooter());
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
        @unlink($this->htmlBodyPath);

        return $this->getFullPath();
    }


    /**
     * @return string
     */
    public function getFormat()
    {
        return !empty($this->config['page']['format']) ? $this->config['page']['format'] : 'A4';
    }

    /**
     * @param string $format
     * @return PdfExporter
     */
    public function setFormat($format)
    {
        if (in_array($format, ['A4', 'A3', 'Letter']) ) $this->config['page']['format'] = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return !empty($this->config['page']['orientation']) ? $this->config['page']['orientation'] : 'portrait';
    }

    /**
     * @param $orientation
     * @return PdfExporter
     */
    public function setOrientation($orientation){
        if( in_array($orientation, ['landscape', 'portrait']) ) $this->config['page']['orientation'] = $orientation;
        return $this;
    }

    /**
     * @return string
     */
    public function getMargin()
    {
        return !empty($this->config['page']['margin'])
            ? $this->config['page']['margin']
            : '{top: "20px", right: "20px", bottom: "20px", left: "20px"}';
    }

    /**
     * @param string $margin
     * @return PdfExporter
     */
    public function setMargin($margin)
    {
        $this->config['page']['margin'] =(str_replace('{', '', $margin) == $margin) ? '"'.$margin.'"' : $margin;
        return $this;
    }

    /**
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
    public function setCommandOptions(array $options)
    {
        $this->commandOptions = $options;
        return $this;
    }

    /**
     * @param string  $option
     * @param string $value
     * @return PdfExporter
     */
    public function addCommandOption($option, $value)
    {
        if ( isset($this->configValidOptions[$option])) {
            $validOptions = $this->configValidOptions[$option];
            if (is_array($validOptions) && in_array($value, $validOptions)) {
                $this->commandOptions[$option] = $value;
                return $this;
            }
            switch ($validOptions) {
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

        return $this;
    }

    /**
     * Prefix the input path for windows versions of PhantomJS
     * @param string $path
     * @param string $operationalSystem
     * @return string
     */
    public function prefixOsPath($path, $operationalSystem = PHP_OS)
    {
        if (strtoupper(substr($operationalSystem, 0, 3)) === 'WIN') {
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
            
            page.viewportSize = { width: '.$this->getViewPortWidth().', height: '.$this->getViewPortHeight().'};
            
            page.paperSize = {
                format: "'.$this->getFormat().'",
                orientation: "'.$this->getOrientation().'",
                margin: '.$this->getMargin().',
                footer: {
                    '.$this->getFooterScript().'
                },
                header: {
                    '.$this->getHeaderScript().'
                },
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

    public function getFooterHeight()
    {
        return isset($this->config['footer']['height']) ? $this->config['footer']['height'] : '25px';
    }

    public function getHeaderHeight()
    {

        return isset($this->config['header']['height']) ? $this->config['header']['height'] : '45px';
    }

    protected function getHeaderScript()
    {
        return $this->getScript($this->htmlHeader, $this->getHeaderHeight());
    }

    protected function getFooterScript()
    {
        return $this->getScript($this->htmlFooter, $this->getFooterHeight());
    }

    protected function getScript($html, $heigth)
    {
        if (empty($html)) return '';
        $script = 'height: "'.$heigth.'",';
        $script.= 'contents: phantom.callback(function(numPage, totalPages) {';
        $script.= ' return "'.$html.'";"';
        $script.= '"})';
        return $script;
    }

    protected function getViewPortWidth()
    {
        return $this->config['page']['orientation'] == 'landscape' ? '3508' : '2480';
    }

    protected function getViewPortHeight()
    {
        return $this->config['page']['orientation'] == 'landscape' ? '2480' : '3508';
    }

    protected function processInlineHtml($html)
    {
        $clearHtml = str_replace('"', '\'', $html);
        $clearHtml = str_replace("@{{", '" + ', $clearHtml);
        return $this->compress(str_replace("}}", ' + "', $clearHtml));
    }
}