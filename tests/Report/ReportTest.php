<?php
namespace Fireguard\Report;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    public function testReportConstructor()
    {
        $report = new Report('content value', 'header value', 'footer value', [ 'config' => 'values' ]);
        $this->assertEquals('content value', $report->getContent());
        $this->assertEquals('header value', $report->getHeader());
        $this->assertEquals('footer value', $report->getFooter());
        $this->assertEquals([ 'config' => 'values' ], $report->getConfig());
    }

    public function testReportContentInConstructor()
    {
        $report = new Report('string for test text values');
        $this->assertEquals('string for test text values', $report->getContent());

        $report = new Report('<div>test for html values</div>');
        $this->assertEquals('<div>test for html values</div>', $report->getContent());

        $report = new Report('');
        $this->assertEquals('', $report->getContent());
    }

    public function testReportContentInSet()
    {
        $report = (new Report(''))->setContent('string for test text values');
        $this->assertEquals('string for test text values', $report->getContent());

        $report->setContent('<div>test for html values</div>');
        $this->assertEquals('<div>test for html values</div>', $report->getContent());

        $report->setContent('');
        $this->assertEquals('', $report->getContent());
    }

    public function testReportHeaderInConstructor()
    {
        $report = new Report('', 'string for test text values');
        $this->assertEquals('string for test text values', $report->getHeader());

        $report = new Report('', '<div>test for html values</div>');
        $this->assertEquals('<div>test for html values</div>', $report->getHeader());

        $report = new Report('', '');
        $this->assertEquals('', $report->getHeader());
    }

    public function testReportHeaderInSet()
    {
        $report = (new Report(''))->setHeader('string for test text values');
        $this->assertEquals('string for test text values', $report->getHeader());

        $report->setHeader('<div>test for html values</div>');
        $this->assertEquals('<div>test for html values</div>', $report->getHeader());

        $report->setHeader('');
        $this->assertEquals('', $report->getHeader());
    }

    public function testReportFooterInConstructor()
    {
        $report = new Report('', '', 'string for test text values');
        $this->assertEquals('string for test text values', $report->getFooter());

        $report = new Report('', '', '<div>test for html values</div>');
        $this->assertEquals('<div>test for html values</div>', $report->getFooter());

        $report = new Report('', '', '');
        $this->assertEquals('', $report->getFooter());
    }

    public function testReportFooterInSet()
    {
        $report = (new Report(''))->setFooter('string for test text values');
        $this->assertEquals('string for test text values', $report->getFooter());

        $report->setFooter('<div>test for html values</div>');
        $this->assertEquals('<div>test for html values</div>', $report->getFooter());

        $report->setFooter('');
        $this->assertEquals('', $report->getFooter());
    }

    public function testReportAppendImagesToHeaderAndFooterInContent()
    {
        $report = new Report('<div>test for html values</div>');
        $report->setHeader('<div><img src="img-src-path.png" title="Description"></div>');

        $expected = '<img src="img-src-path.png" style="display: none;" /><div>test for html values</div>';
        $this->assertEquals($expected, $report->getContent());

        $report = new Report('<div>test for html values</div>');
        $report->setFooter('<div><img src="img-src-path.png" title="Description"></div>');

        $expected = '<img src="img-src-path.png" style="display: none;" /><div>test for html values</div>';
        $this->assertEquals($expected, $report->getContent());
    }

    public function testReportConfigInConstructor()
    {
        $report = new Report('', '', '', [ 'size' => 10 ]);
        $this->assertEquals([ 'size' => 10 ], $report->getConfig());

        $report = new Report('', '', '', []);
        $this->assertEquals([], $report->getConfig());
    }

    public function testReportConfigInSet()
    {
        $report = (new Report(''))->setConfig([ 'size' => 10 ]);
        $this->assertEquals([ 'size' => 10 ], $report->getConfig());

        $report->setConfig([]);
        $this->assertEquals([], $report->getConfig());
    }
}
