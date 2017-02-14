<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterInterface;
use Fireguard\Report\Contracts\ReportInterface;
use PhantomInstaller\PhantomBinary;

class ImageExporter extends AbstractPhantomExporter  implements ExporterInterface
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
     * @return ExporterInterface
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
     * @param ReportInterface $report
     * @return string
     */
    public function generate(ReportInterface $report)
    {
        $this->htmlBodyPath = $this->generateHtmlWithAllReportContent($report);
        return $this->saveFinishFile();
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return 'image/'.mb_strtolower($this->format);
    }

    protected function generateHtmlWithAllReportContent(ReportInterface $report)
    {
        $exporter = new HtmlExporter($this->getPath(), $this->fileName);
        return $exporter->generate($report);
    }
}
