<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterContract;
use Fireguard\Report\Contracts\ReportContract;
use PhantomInstaller\PhantomBinary;
use RuntimeException;
use Symfony\Component\Process\Process;

class ImageExporter extends AbstractPhantomExporter  implements ExporterContract
{
    /**
     * @var string ['BMP', 'JPG', 'JPEG', 'PNG']
     */
    protected $format = 'JPG';

    protected $validFormats = ['BMP', 'JPG', 'JPEG', 'PNG'];

    /**
     * @var string ['landscape', 'portrait']
     */
    protected $orientation = 'portrait';

    /**
     * @param array $config
     * @return ExporterContract
     */
    public function configure(array $config = [])
    {
        $this->extension = '.'.strtolower($this->format);
        $defaultConfig = $this->getDefaultConfiguration();
        $this->config = array_replace_recursive($defaultConfig['image'] , $config);

        $this->setConfigDefaultOptions($this->config['phantom']);

        $this->commandOptions = $this->configDefaultOptions;
        $this->setBinaryPath((new PhantomBinary)->getBin());

        return $this;
    }

    /**
     * @param string $format
     * @return AbstractPhantomExporter
     */
    public function setFormat($format)
    {
        parent::setFormat($format);
        $this->extension = '.'.strtolower($this->getFormat());
        return $this;
    }

    /**
     * @param ReportContract $report
     * @return string
     */
    public function generate(ReportContract $report)
    {
        $this->htmlBodyPath = $this->generateHtmlWithAllReportContent($report);
        return $this->saveFinishFile();
    }

    protected function generateHtmlWithAllReportContent(ReportContract $report)
    {
        $exporter = new HtmlExporter($this->getPath(), $this->fileName);
        return $exporter->generate($report);
    }
}