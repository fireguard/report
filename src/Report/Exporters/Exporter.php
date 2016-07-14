<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterContract;

abstract class Exporter implements ExporterContract
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $path;

    /**
     * ExporterContract constructor.
     * @param string $path
     * @param string $fileName
     * @param array $config
     * @return Exporter
     */
    public function __construct($path = '', $fileName = '', $config = [])
    {
        $this->setPath($path);
        $this->setFileName($fileName);
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @param unix_permission $mode Permission
     * @return Exporter
     */
    public function setPath($path, $mode = 0777)
    {
        $tmpPath = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if ( empty($path)
            || ( !file_exists($path) && !mkdir($path, $mode, true) )
            || ( !empty($this->fileName) && !(touch($tmpPath.$this->getFileName(), $mode)))
        ) {
            $this->path = sys_get_temp_dir().DIRECTORY_SEPARATOR;
            return $this;
        }

        $this->path = $tmpPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param $fileName
     * @return Exporter
     */
    public function setFileName($fileName)
    {
        if ( empty($fileName)
            || (file_exists($this->getPath().$fileName) && !is_writable($this->getPath().$fileName) )
            || !touch($this->getPath().$fileName)
        ) {
            $this->fileName = str_replace(
                $this->getPath(), '', tempnam( rtrim($this->getPath(), DIRECTORY_SEPARATOR), 'report-')
            );
            return $this;
        }

        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->getPath().$this->getFileName();
    }


    /**
     * @return string
     */
    abstract function generate();
}