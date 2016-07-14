<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterContract;
use Fireguard\Report\Contracts\ReportContract;

class HtmlExporter extends Exporter implements ExporterContract
{

    public function initialize()
    {
        // TODO
    }

    public function generate(ReportContract $report)
    {
        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$this->fileName.'</title></head>';
        $html.= '<body>'.$report->getHeader().$report->getContent().$report->getFooter().'</body></html>';
        return $this->saveFile($html);
    }

    public function saveFile($content)
    {
        try {
            file_put_contents($this->getFullPath(), $content);
            return $this->getFullPath();
        } catch (\Exception $e) {
            return false;
        }
    }

}