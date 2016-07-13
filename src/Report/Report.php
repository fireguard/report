<?php
namespace Fireguard\Report;


use Fireguard\Report\Contracts\ReportContract;

class Report implements ReportContract
{
    /**
     * @var string
     */
    private $content;
    /**
     * @var string
     */
    private $header;
    /**
     * @var string
     */
    private $footer;
    /**
     * @var array
     */
    private $config;

    /**
     * ReportContract constructor.
     * @param string $content
     * @param string $header
     * @param string $footer
     * @param array $config
     * @return Report
     */
    public function __construct($content, $header = "", $footer = "", array $config = [])
    {

        $this->content = $content;
        $this->header = $header;
        $this->footer = $footer;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return ReportContract
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     * @return Report
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return string
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param string $footer
     * @return Report
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $config
     * @return Report
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }
}