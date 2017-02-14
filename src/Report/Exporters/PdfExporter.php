<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterInterface;
use Fireguard\Report\Contracts\ReportInterface;
use PhantomInstaller\PhantomBinary;

class PdfExporter extends AbstractPhantomExporter  implements ExporterInterface
{
    /**
     * @var string ['A4', 'A3', 'Letter']
     */
    protected $format = 'A4';

    protected $validFormats = ['A4', 'A3', 'Letter'];

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
        $this->extension = '.pdf';
        $defaultConfig = $this->getDefaultConfiguration();
        $this->config = array_replace_recursive($defaultConfig['pdf'] , $config);

        $this->setConfigDefaultOptions($this->config['phantom']);

        $this->commandOptions = $this->configDefaultOptions;
        $this->setBinaryPath((new PhantomBinary)->getBin());

        return $this;
    }

    /**
     * @param ReportInterface $report
     * @return string
     */
    public function generate(ReportInterface $report)
    {
        $this->createHtmlFiles($report);
        return $this->saveFinishFile();
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return 'application/pdf';
    }
}
