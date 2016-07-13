<?php
namespace Fireguard\Report\Contracts;

interface ReportContract
{
    /**
     * ReportContract constructor.
     * @param string $content
     * @param string $header
     * @param string $footer
     * @param array $config
     * @return ReportContract
     */
    public function __construct($content, $header = "", $footer = "", array $config = []);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     * @return ReportContract
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getHeader();

    /**
     * @param string $header
     * @return ReportContract
     */
    public function setHeader($header);

    /**
     * @return string
     */
    public function getFooter();

    /**
     * @param string $footer
     * @return ReportContract
     */
    public function setFooter($footer);

    /**
     * @return string
     */
    public function getConfig();

    /**
     * @param string $config
     * @return ReportContract
     */
    public function setConfig($config);
}