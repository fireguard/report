<?php
include "../vendor/autoload.php";

$html = file_get_contents('report1.html');

$header = '<div style="text-align: center;font-size: 20px; border-bottom: 1px #eeeeee solid; padding: 1px; ">';
$header.= '    <strong>THE MANAGEMENT REPORT TITLE</strong>';
$header.= '</div>';

$footer = '<div style="text-align: right;font-size: 10px; border-top: 1px #eeeeee solid; padding: 2px;">';
$footer.= '    Page <span>@{{ numPage }} of @{{ totalPages }}</span>';
$footer.= '</div>';

$report = new \Fireguard\Report\Report($html, $header, $footer);
$exporter = new \Fireguard\Report\Exporters\PdfExporter('', 'report1-to-pdf');

// Option 1
// Return with Symfony\Component\HttpFoundation\Response
$exporter
    ->setOrientation('landscape')
    ->setFormat('A3')
    ->setMargin('0px')
    ->response($report)
    ->send();


// Option 2
// Manual Return
$file = $exporter->setOrientation('landscape')
    ->setFormat('A3')
    ->setMargin('0px')
    ->generate($report);

header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="report1-to-pdf.pdf"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file));
header('Accept-Ranges: bytes');

@readfile($file);
