<?php
namespace Fireguard\Report\Exporters;


use Fireguard\Report\Report;

class HtmlExporterTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerateCompleteReportFile()
    {
        $exporter = new HtmlExporter();
        $report = new Report(
            '<section class="content">Content</section>',
            '<section class="header">Header</section>',
            '<section class="footer">Footer</section>'
        );
        $file = $exporter->generate($report);
        $this->assertFileExists($file);
        $expectHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$exporter->getFileName().'</title></head><body><section class=\'header\'>Header</section><section class="content">Content</section><section class=\'footer\'>Footer</section></body></html>';
        $this->assertStringEqualsFile($file, $expectHtml);
    }

    public function testGenerateOnlyHeaderReportFile()
    {
        $exporter = new HtmlExporter();
        $report = new Report(
            '<section class="content">Content</section>',
            '<section class="header">Header</section>'
        );
        $file = $exporter->generate($report);
        $this->assertFileExists($file);
        $expectHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$exporter->getFileName().'</title></head><body><section class=\'header\'>Header</section><section class="content">Content</section></body></html>';
        $this->assertStringEqualsFile($file, $expectHtml);
    }

    public function testGenerateOnlyFooterReportFile()
    {
        $exporter = new HtmlExporter();
        $report = new Report(
            '<section class="content">Content</section>',
            '',
            '<section class="footer">Footer</section>'
        );
        $file = $exporter->generate($report);
        $this->assertFileExists($file);
        $expectHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$exporter->getFileName().'</title></head><body><section class="content">Content</section><section class=\'footer\'>Footer</section></body></html>';
        $this->assertStringEqualsFile($file, $expectHtml);
    }

    public function testCreateReportFile()
    {
        $exporter = new HtmlExporter();
        $file = $exporter->saveFile('<div>Aqui entra um html qualquer</div>');
        $this->assertFileExists($file);
        $this->assertStringEqualsFile($file, '<div>Aqui entra um html qualquer</div>');
    }
}