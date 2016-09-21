<?php
namespace Fireguard\Report\Contracts;

interface ExporterContract
{
    /**
     * ExporterContract constructor.
     * @param string $path
     * @param string $fileName
     * @param array $config
     * @return ExporterContract
     */
    public function __construct($path = '', $fileName = '', $config = []);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     * @param unix_permission $mode Permission
     * @return ExporterContract
     */
    public function setPath($path, $mode = 0777);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @param $fileName
     * @return ExporterContract
     */
    public function setFileName($fileName);

    /**
     * @return string
     */
    public function getFullPath();

    /**
     * @param array $config
     * @return ExporterContract
     */
    public function configure(array $config);

    /**
     * Compress html e js removed comments e break lines
     * @param $buffer
     * @return mixed
     */
    public function compress($buffer);

    /**
     * @param ReportContract $report
     * @return string | false
     */
    public function generate(ReportContract $report);

}