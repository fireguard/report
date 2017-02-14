<?php
namespace Fireguard\Report\Contracts;

interface ReportInterface
{
    /**
     * ReportInterface constructor.
     * @param string $content
     * @param string $header
     * @param string $footer
     * @param array $config
     * @return ReportInterface
     */
    public function __construct($content, $header = "", $footer = "", array $config = []);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     * @return ReportInterface
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getHeader();

    /**
     * @param string $header
     * @return ReportInterface
     */
    public function setHeader($header);

    /**
     * @return string
     */
    public function getFooter();

    /**
     * @param string $footer
     * @return ReportInterface
     */
    public function setFooter($footer);

    /**
     * @return string
     */
    public function getConfig();

    /**
     * @param string $config
     * @return ReportInterface
     */
    public function setConfig($config);
}
