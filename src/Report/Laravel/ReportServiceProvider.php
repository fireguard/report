<?php
namespace Fireguard\Report\Laravel;

use Fireguard\Report\Contracts\ExporterInterface;
use Fireguard\Report\Contracts\ReportInterface;
use Fireguard\Report\Exporters\HtmlExporter;
use Fireguard\Report\Exporters\ImageExporter;
use Fireguard\Report\Exporters\PdfExporter;
use Fireguard\Report\Report;
use Illuminate\Support\ServiceProvider;

class ReportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../../config/report.php' => config_path('report.php')
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../../config/report.php', 'report');

        $configDefaultExporter = $this->app['config']['report.default-exporter'];
        $defaultExporter = (class_exists($configDefaultExporter)) ? $configDefaultExporter : PdfExporter::class;

        $storagePath = ( !empty($this->app['config']['report.storage-path']) && file_exists($this->app['config']['report.storage-path']))
                        ? $this->app['config']['report.storage-path']
                        : storage_path('app');

        // Register Default Exporter
        $this->app->bind( ExporterInterface::class, $defaultExporter );

        $this->app->bind( ReportInterface::class, Report::class );

        $this->app->bind( HtmlExporter::class, function ($app) use ($storagePath) {
            return (new HtmlExporter())
                ->setPath($storagePath)
                ->configure($app['config']['report.html']);
        });

        $this->app->bind( PdfExporter::class, function ($app) use ($storagePath) {
            return (new PdfExporter())
                ->setPath($storagePath)
                ->configure($app['config']['report.pdf']);
        });

        $this->app->bind( ImageExporter::class, function ($app) use ($storagePath) {
            return (new ImageExporter())
                ->setPath($storagePath)
                ->configure($app['config']['report.image']);
        });
    }

}
