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
     * @param string $mode Permission
     * @return ExporterContract
     */
    public function setPath($path, $mode = '0777');

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
     * @return string
     */
    public function generate();
}