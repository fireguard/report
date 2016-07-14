<?php
namespace Fireguard\Report;


use Fireguard\Report\Contracts\ExporterContract;
use Fireguard\Report\Contracts\ReportContract;

class ReportGenerator
{
    /**
     * @var ReportContract
     */
    protected $report;
    /**
     * @var ExporterContract
     */
    protected $exporter;

    /**
     * ReportContract constructor.
     * @param ReportContract $report
     * @param ExporterContract $exporter
     * @param array $config
     */
    public function __construct(ReportContract $report, ExporterContract $exporter)
    {
        $this->report = $report;
        $this->exporter = $exporter;
    }

    /**
     * @return string | false
     */
    public function generate()
    {
        return $this->exporter->generate($this->report);
    }

}