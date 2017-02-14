<?php
namespace Fireguard\Report;


use Fireguard\Report\Contracts\ReportInterface;

class Report implements ReportInterface
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
     * ReportInterface constructor.
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
        $resources = $this->getResourcesStringInHeaderAndFooter();
        return $resources.$this->content;
    }

    /**
     * @param string $content
     * @return ReportInterface
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

    protected function getResourcesStringInHeaderAndFooter()
    {
        $images = $this->getAllImagesInHeaderAndFooter();
        $resources = '';
        foreach ($images as $image) {
            $resources .= '<img src="'.$image.'" style="display: none;" />';
        }
        return $resources;
    }

    protected function getAllImagesInHeaderAndFooter()
    {
        $doc = new \DOMDocument();
        $html = $this->getHeader().$this->getFooter();
        if (!empty($html)) {
            libxml_use_internal_errors(true);
            $doc->loadHTML($html);
            libxml_clear_errors();
            $imageTags = $doc->getElementsByTagName('img');

            $result = [];
            foreach($imageTags as $tag) {
                $result[] = $tag->getAttribute('src');
            }
            return array_unique($result);
        }
        return [];
    }
}
