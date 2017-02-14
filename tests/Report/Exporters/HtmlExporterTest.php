<?php
namespace Fireguard\Report\Exporters;


use Fireguard\Report\Report;
use Symfony\Component\HttpFoundation\Response;

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
        $expectHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$exporter->getFileName().'</title></head><body style="background-color: #ffffff;"><section class=\'header\'>Header</section><section class="content">Content</section><section class=\'footer\'>Footer</section></body></html>';
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
        $expectHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$exporter->getFileName().'</title></head><body style="background-color: #ffffff;"><section class=\'header\'>Header</section><section class="content">Content</section></body></html>';
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
        $expectHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$exporter->getFileName().'</title></head><body style="background-color: #ffffff;"><section class="content">Content</section><section class=\'footer\'>Footer</section></body></html>';
        $this->assertStringEqualsFile($file, $expectHtml);
    }

    public function testCreateResponseInline()
    {
        $exporter = new HtmlExporter(null, 'test-file-name-inline');
        $report = new Report('<section class="content">Content</section>');
        $response = $exporter->response($report);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertFalse(
            $response->headers->contains('content-disposition', 'attachment; filename="test-file-name-inline.html"'),
            'Could not find header to force download'
        );


        $expectHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$exporter->getFileName().'</title></head><body style="background-color: #ffffff;"><section class="content">Content</section></body></html>';
        $this->assertEquals($expectHtml, $response->getContent());
    }

    public function testCreateResponseForceDownload()
    {
        $exporter = new HtmlExporter(null, 'test-file-name-download');
        $report = new Report('<section class="content">Content</section>');
        $response = $exporter->response($report, true);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertTrue(
            $response->headers->contains('content-disposition', 'attachment; filename="test-file-name-download.html"'),
            'Could not find header to force download'
        );

        $expectHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$exporter->getFileName().'</title></head><body style="background-color: #ffffff;"><section class="content">Content</section></body></html>';
        $this->assertEquals($expectHtml, $response->getContent());
    }


    public function testCreateReportFile()
    {
        $exporter = new HtmlExporter();
        $file = $exporter->saveFile('<div>Aqui entra um html qualquer</div>');
        $this->assertFileExists($file);
        $this->assertStringEqualsFile($file, '<div>Aqui entra um html qualquer</div>');
    }
}
