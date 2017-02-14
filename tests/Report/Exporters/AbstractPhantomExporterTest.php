<?php
namespace Fireguard\Report\Exporters;

class AbstractPhantomExporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PdfExporter
     */
    protected $exporter;

    /**
     * @var array
     */
    protected $configDefault;

    public function setUp()
    {
        parent::setUp();
        $this->exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $tmpConfig = $this->exporter->getDefaultConfiguration();
        $this->configDefault = $tmpConfig['pdf'];
    }

    public function testGetDefaultOrientation()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $this->assertEquals($this->configDefault['page']['orientation'], $exporter->getOrientation());
    }

    public function testSetValidOrientation()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $exporter->setOrientation('landscape');
        $this->assertEquals('landscape', $exporter->getOrientation());

    }

    public function testSetInvalidOrientation()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $exporter->setOrientation('invalid-orientation');
        $this->assertEquals($this->configDefault['page']['orientation'], $exporter->getOrientation());
    }

    public function testGetMargin()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $this->assertEquals($this->configDefault['page']['margin'], $exporter->getMargin());
    }

    public function testSetMargin()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $exporter->setMargin('{top: "0px", right: "0px", bottom: "0px", left: "0px"}');
        $this->assertEquals('{top: "0px", right: "0px", bottom: "0px", left: "0px"}', $exporter->getMargin());

        $exporter->setMargin('5px');
        $this->assertEquals('"5px"', $exporter->getMargin());

    }

    public function testSetCommandOptions()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $options = [
            'debug' => false,
            'ignore-ssl-errors' => true,
            'load-images' => true,
            'ssl-protocol' => 'any'
        ];
        $exporter->setCommandOptions($options);
        $this->assertEquals($options, $exporter->getCommandOptions());
    }

    public function testSetBinayPath()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $exporter->setBinaryPath('/path/for/binary');
        $this->assertEquals('/path/for/binary', $exporter->getBinaryPath());
    }

    public function testPrefixerFilePath()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);

        $this->assertEquals('/var/www', $exporter->prefixOsPath('/var/www', 'LINUX'));

        $this->assertEquals('file:///c:/www', $exporter->prefixOsPath('c:/www', 'WIN'));
    }

    public function testAddCommandOption()
    {
        $exporter = $this->getMockForAbstractClass(AbstractPhantomExporter::class);
        $exporter->setConfigValidOptions([
            'web-security' => 'bool',
            'disk-cache' => 'bool',
            'local-storage-path' => 'string',
            'test-option' => 'not-validated-type',
            'ssl-protocol' => [ 'sslv3', 'sslv2', 'tlsv1', 'any']
        ]);

        // Ignore Invalid Option
        $options = $exporter->getCommandOptions();
        $exporter->addCommandOption('command-option-include', true);
        $this->assertEquals($options, $exporter->getCommandOptions());

        // Ignore Invalid Value
        $exporter->addCommandOption('web-security', 'invalid-expected-bool');
        $this->assertEquals($options, $exporter->getCommandOptions());

        // Define Valid Value for Bool
        $exporter->addCommandOption('disk-cache', true);
        $this->assertArrayHasKey('disk-cache', $exporter->getCommandOptions());

        // Define Valid Value for Array
        $exporter->addCommandOption('ssl-protocol', 'any');
        $options = $exporter->getCommandOptions();
        $this->assertArrayHasKey('ssl-protocol', $options);
        $this->assertEquals('any', $options['ssl-protocol']);

        // Define Valid Value for String
        $exporter->addCommandOption('local-storage-path', 'path-string');
        $options = $exporter->getCommandOptions();
        $this->assertArrayHasKey('local-storage-path', $options);
        $this->assertEquals('path-string', $options['local-storage-path']);

        // Define Valid Value for not validated format
        $exporter->addCommandOption('test-option', 'any-value');
        $options = $exporter->getCommandOptions();
        $this->assertArrayHasKey('test-option', $options);
        $this->assertEquals('any-value', $options['test-option']);
    }
}
