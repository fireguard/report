<?php
namespace Fireguard\Report\Contracts;

interface ExporterInterface
{
    /**
     * ExporterInterface constructor.
     * @param string $path
     * @param string $fileName
     * @param array $config
     * @return ExporterInterface
     */
    public function __construct($path = '', $fileName = '', $config = []);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     * @param unix_permission $mode Permission
     * @return ExporterInterface
     */
    public function setPath($path, $mode = 0777);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @param $fileName
     * @return ExporterInterface
     */
    public function setFileName($fileName);

    /**
     * @return string
     */
    public function getFullPath();

    /**
     * @param array $config
     * @return ExporterInterface
     */
    public function configure(array $config);

    /**
     * Compress html e js removed comments e break lines
     * @param $buffer
     * @return mixed
     */
    public function compress($buffer);

    /**
     * @param ReportInterface $report
     * @return string | false
     */
    public function generate(ReportInterface $report);

    /**
     * @param boolean $forceDownload
     * @param ReportInterface $report
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(ReportInterface $report, $forceDownload = false);
}
