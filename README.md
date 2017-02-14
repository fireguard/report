# Fireguard Report

[![Build Status](https://travis-ci.org/fireguard/report.png)](https://travis-ci.org/fireguard/report)
[![Latest Stable Version](https://poser.pugx.org/fireguard/report/v/stable)](https://packagist.org/packages/fireguard/report)
[![Latest Unstable Version](https://poser.pugx.org/fireguard/report/v/unstable)](https://packagist.org/packages/fireguard/report)
[![Total Downloads](https://poser.pugx.org/fireguard/report/downloads)](https://packagist.org/packages/fireguard/report)
[![License](https://poser.pugx.org/fireguard/report/license)](https://packagist.org/packages/fireguard/report)
[![Code Climate](https://codeclimate.com/github/fireguard/report/badges/gpa.svg)](https://codeclimate.com/github/fireguard/report)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f378db1b-5a6a-4b13-b49d-ff28a9c29a62/big.png)](https://insight.sensiolabs.com/projects/f378db1b-5a6a-4b13-b49d-ff28a9c29a62)

**Other languages for this documentation: [PORTUGUÊS](README_PT.md)**

The **Fireguard Report** is a report management package in PHP that aims to help you export information in a variety of 
formats, such as HTML, PDF and IMAGE, using a unique, integrated and simple interface.

<div id='summary'/>
# Summary 

- [Installation](#install)
    - [Installing and Updating PhantomJs](#install-phantom)
- [How to use](#use)
    - [Generating our first report](#first-report)
    - [Header and Footer](#footer-header)
    - [Exporters](#exporters)
        - [Methods available in all Exports](#methods-exports)
        - [HtmlExporter](#html-exporter)
        - [PdfExporter](#pdf-exporter)
        - [ImageExporter](#image-exporter)
        
- [Laravel](#laravel)
    - [Registering Service Provider](#laravel-register-provider)
    - [Publishing the configuration file](#laravel-publish-config)
    - [Examples of use with Laravel (Dependency Injection)](#laravel-use)
- [Other usage examples](#examples)
    - [Generating an HTML report](#use-link-html)
    - [Generating an PDF report](#use-link-pdf)
    - [Generating an IMAGE report](#use-link-image)
    - [Sample ticket generated with this package](#use-link-boleto)


# <div id="install" />Installation

The FireGuard Report can be installed through the composer.

In order for the package to be automatically added to your composer.json file, run the following command:

```bash
  composer require fireguard/report
```

Or if you prefer, add the following snippet manually:

```
{
  "require": {
    ...
    "fireguard/report": "^0.1"
  }
}
```

## <div id="install-phantom"/>Installing and Updating PhantomJs

To generate the PDF files and Images, this package is used by PhantomJs. For installation and update, we suggest two 
options:

**1st Option:** Add the lines below in the composer.json file, so the installation and update process will occur every 
time you execute the "composer install" or "composer update" commands.

```
  "scripts": {
    "post-install-cmd": [
      "PhantomInstaller\\Installer::installPhantomJS"
    ],
    "post-update-cmd": [
      "PhantomInstaller\\Installer::installPhantomJS"
    ]
  }
```

**2st Option:** If you do not want to always keep the latest PhantomJS version, one possibility is to add a new script 
in composer.json as shown below:

```
  "scripts": {
    "update-phantomjs": [
      "PhantomInstaller\\Installer::installPhantomJS"
    ]
  }
```

And run whenever you want to update the version of the executable the following command ``composer run-script update-phantomjs``

If you choose this option, you must run at least the first time for it to be installed.


# <div id="use"/>How to use

## <div id="first-report"/>Generating our first report

The use of this package is very simple, we will need two objects to generate a final file, the first one is Report, 
with it we define the effective content of the report, the second is the Exporter, which receives a Report and is 
responsible for handling the information and export to a final file.

Here is a simple example to generate a file:

```php
    $report     = new \Fireguard\Report\Report('<h1>Report Title</h1>');
    $exporter   = new \Fireguard\Report\Exporters\PdfExporter();
    $file       = $exporter->generate($report);    
```

So at the end of the execution, in the variable **$file** we will have the actual path to the generated file.

## <div id="footer-header" />Header and Footer

For header and footer HTML, two variables are available in exporters that use paging, such as PdfExporter, **numPage**, 
and **totalPages**, which contains the current page and the total pages of the page respectively. 
To access them, you must enclose them by "@{{  }}", so the contents of the content will be automatically updated.
Below is a simple example that will use the header and footer;

```php 
  $html   = file_get_contents('report.html');
  
  $header = '<div style="text-align: center;font-size: 20px; border-bottom: 1px #eeeeee solid; padding: 1px; ">';
  $header.= '    <strong>THE MANAGEMENT REPORT TITLE</strong>';
  $header.= '</div>';

  $footer = '<div style="text-align: right;font-size: 10px; border-top: 1px #eeeeee solid; padding: 2px;">';
  $footer.= '    Page <span>@{{ numPage }} of @{{ totalPages }}</span>';
  $footer.= '</div>';
  
  $report = new \Fireguard\Report\Report($html, $header, $footer);
  $exporter = new \Fireguard\Report\Exporters\PdfExporter('.', 'report1-to-pdf');
  $file   = $exporter->generate($report);
  
```
With this example above we will find in the variable **$file** the path to the generated PDF file;

## <div id="exporters" />Exporters

As we saw in the previous examples, the export of the report requires an Exporter class. An Exporter is a specialized 
class that implements an ExporterInterface interface and is responsible for catching a Report object and transforming it 
into a finalized file. 

At this point we have included in the package three Exporters, one for HTML, one for PDF and one for Images, it is 
possible that in future new Exporters will be available, we also encourage you to develop new Exporters and, if 
possible, contribute to the project.

### <div id="methods-exports" />Methods available in all Exports

``getPath()``: Returns the location where the generated file will be saved;

``setPath($path, $mode = 0777)``: Sets the location where the file should be saved;

``getFileName()``: Returns the name of the file to save;

``setFileName($fileName)``: Sets the name of the file to be saved;

``getFullPath()``: Returns the complete path with the name of the file to be generated;

``compress($buffer)``: Returns a compressed string with no comments or line breaks;

``configure(array $config)``: Sets the settings to apply to the current report;

``generate(ReportInterface $report)``: Renders the report and returns a path to the generated file;

``response(ReportInterface $report, $forceDownload)``: Renders the report and returns an instance of the Symfony\Component\HttpFoundation\Response;

Example of use with a fluent interface:

```php
$report = new \Fireguard\Report\Report('<h1>Report Title</h1>');
$exporter = new \Fireguard\Report\Exporters\PdfExporter();
// Example returning an HTTP response
$exporterGenerates report
    ->setPath('.') // Sets the save to the local folder
    ->setFileName('report.pdf') // Define as 'report.pdf' the name of the file to be generated
    ->configure(['footer' => ['height' => '30px']) // Set the footer size to 30px
    ->response($report) // Create an HTTP response
    ->send(); // Returns the response to the user
    
// Example generating a local file
$file = $exporter
    ->setPath('.') // Sets the save to the local folder
    ->setFileName('report.pdf') // Define as 'report.pdf' the name of the file to be generated
    ->configure(['footer' => ['height' => '30px']) // Set the footer size to 30px
    ->generate($report); // Generates report
          
```

### <div id="html-exporter" /> HtmlExport

For exporting files in **HTML** format, in addition to the standard methods, some others are available, below all are 
listed with a brief description of their function:

``saveFile($content)``: Saves the HTML file and returns the full path to the generated file;

### <div id="pdf-exporter" />PdfExport

For exporting files in **PDF** format, in addition to the standard methods, some others are available, below all are 
listed with a brief description of their function:

``getFormat()``: Returns the defined paper size;

``setFormat($format)``: Sets a paper size to be exported. (Valid Formats: 'A4', 'A3', 'Letter')

``getOrientation()``: Returns the orientation of the defined paper;

``setOrientation($orientation)``: Sets the orientation of the paper to be exported. (Valid Orientations: 'landscape', 'portrait')

``getMargin()``: Returns the defined paper margin;

``setMargin($margin)``: Sets the paper margin to be exported;

``getBinaryPath()``: Returns the path to the PhantomJS binary in the application;

``setBinaryPath($binaryPath)``: Sets the path to the PhantomJS binary file in the application;

``getCommandOptions()``: Returns the parameters to be executed with the PhantomJS for export;

``setCommandOptions(array $options)``: Sets the parameters to be executed with the PhantomJS for export;

``addCommandOption($option, $value)``: Adds a new parameter to run with PhantomJS for export;

``getHeaderHeight()``: Returns the size of the header defined;

``getFooterHeight()``: Returns the size of the footer defined;

### <div id="image-exporter" />ImageExport

For exporting files in **IMAGE** format, in addition to the standard methods, some others are available, below all are 
listed with a brief description of their function:

``getFormat()``: Returns the defined paper size;

``setFormat($format)``: Sets a paper size to be exported. (Valid Formats: 'A4', 'A3', 'Letter')

``getOrientation()``: Returns the orientation of the defined paper;

``setOrientation($orientation)``: Sets the orientation of the paper to be exported. (Valid Orientations: 'landscape', 'portrait')

``getMargin()``: Returns the defined paper margin;

``setMargin($margin)``: Sets the paper margin to be exported;

``getBinaryPath()``: Returns the path to the PhantomJS binary in the application;

``setBinaryPath($binaryPath)``: Sets the path to the PhantomJS binary file in the application;

``getCommandOptions()``: Returns the parameters to be executed with the PhantomJS for export;

``setCommandOptions(array $options)``: Sets the parameters to be executed with the PhantomJS for export;

``addCommandOption($option, $value)``: Adds a new parameter to run with PhantomJS for export;

``getHeaderHeight()``: Returns the size of the header defined;

``getFooterHeight()``: Returns the size of the footer defined;


# <div id="laravel" /> Laravel

The steps described below are optional and can only make it easier for those who want to use this package with Laravel 5.

## <div id="laravel-register-provider" /> Registering Service Provider

In the ``config\app.php`` configuration file, register above the providers of your application as follows:

```
'providers' => [
    Fireguard\Report\Laravel\ReportServiceProvider::class,
    ...
]
```

## <div id="laravel-publish-config" /> Publishing the configuration file

To publish the configuration file you must use the following command:
```
php artisan vendor:publish --provider="Fireguard\Report\Laravel\ReportServiceProvider"
```

## <div id="laravel-use" /> Examples of use with Laravel (Dependency Injection)

With the registration of the service provider, you can now use the dependency injection of Laravel to solve the 
exporters, already bringing them ready and configured with the application configuration file.

For dependency injection four classes are available, one interface and three concrete, the default interface is solved 
for the concrete PdfExporter class, which can be changed in the ``default-exporter`` parameter of the configuration 
file ``report.php`` generated in the integration. See below some examples of use.

### <div id="laravel-injection-interface" /> Exporter Interface

```php
    public function index (\Fireguard\Report\Exporters\ExporterInterface $exporter)
    {
        $html = view()->make('welcome')->render();
        
        // Option 1
        return $exporter
            ->response(new Report($html))
            ->send();
        
        // Option 2
        $file = $exporter->generate(new Report($html));
        $headers = [
            'Content-type' => mime_content_type($file),
            'Content-Transfer-Encoding' => 'binary',
            'Content-Length' => filesize($file),
            'Accept-Ranges' => 'bytes'
        ];
        // If you want to directly display the file
        return response()->make(file_get_contents($file), 200, $headers);
        // If you want to force download
        // return response()->download($file, 'report.pdf', $headers);
    }
```

### <div id="laravel-injection-html" /> HtmlExporter Class

```php
    public function index (\Fireguard\Report\Exporters\HtmlExporter $exporter)
    {
        $html = view()->make('welcome')->render();
        // Option 1
        return $exporter
            ->response(new Report($html))
            ->send();
            
                
        // Option 2
        $file = $exporter->generate(new Report($html));
        // If you want to directly display the file
        // return response()->make(file_get_contents($file), 200);
        // If you want to force download
        return response()->download($file, 'report.html', []);
    }
```

### <div id="laravel-injection-pdf" /> PdfExporter Class

```php
    public function index (\Fireguard\Report\Exporters\PdfExporter $exporter)
    {
        $html = view()->make('welcome')->render();
        // Option 1
        return $exporter
            ->response(new Report($html))
            ->send();
                    
                        
        // Option 2
        $file = $exporter->generate(new Report($html));
        $headers = [
            'Content-type' => 'application/pdf',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Length' => filesize($file),
            'Accept-Ranges' => 'bytes'
        ];
        // If you want to directly display the file
        return response()->make(file_get_contents($file), 200, $headers);
        // If you want to force download
        // return response()->download($file, 'report.pdf', $headers);
    }
```

### <div id="laravel-injection-image" /> ImageExporter Class

```php
    public function index (\Fireguard\Report\Exporters\ImageExporter $exporter)
    {
        $html = view()->make('welcome')->render();
        
        // Option 1
        return $exporter
            ->response(new Report($html))
            ->send();
                            
                                
        // Option 2
        $file = $exporter->generate(new Report($html));
        $headers = [
            'Content-type' => 'image/jpg',
            'Content-Length' => filesize($file),
        ];
        // If you want to directly display the file
        return response()->make(file_get_contents($file), 200, $headers);
        // If you want to force download
        // return response()->download($file, 'report.jpg', $headers);
    }
```

# <div id="examples" /> Other usage examples
<br />
- <a href="examples/report1-html.php" target="_blank" id="use-link-html"> Generating an HTML report</a>
- <a href="examples/report1-pdf.php" target="_blank" id="use-link-pdf"> Generating an PDF report</a>
- <a href="examples/report1-image.php" target="_blank" id="use-link-image"> Generating an IMAGE report</a>
- <a href="examples/report-boleto.pdf" target="_blank" id="use-link-boleto"> Sample ticket generated with this package</a>
<br />
