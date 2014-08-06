<?php

namespace Tagcade\Tests\Model\Report\PerformanceReport\Display;

use Tagcade\Model\Report\PerformanceReport\Display\AccountReport;
use Tagcade\Model\Report\PerformanceReport\Display\AdSlotReport;
use Tagcade\Model\Report\PerformanceReport\Display\AdTagReport;
use Tagcade\Model\Report\PerformanceReport\Display\PlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\SiteReport;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdTagReport
     */
    protected $adTagReport1;

    /**
     * @var AdTagReport
     */
    protected $adTagReport2;

    /**
     * @var PlatformReport
     */
    protected $platformReport;

    public function setUp()
    {
        $adTagReport1 = (new AdTagReport())
            ->setOpportunities(10)
            ->setImpressions(5)
            ->setPassbacks(5)
        ;

        $this->adTagReport1 = $adTagReport1;

        $adTagReport2 = (new AdTagReport())
            ->setOpportunities(5)
            ->setImpressions(1)
            ->setPassbacks(4)
        ;

        $this->adTagReport2 = $adTagReport2;

        $adSlotReport1 = (new AdSlotReport())
            ->setSlotOpportunities(10)
            ->addSubReport($adTagReport1)
            ->addSubReport($adTagReport2)
        ;

        // end first ad slot

        $adTagReport3 = (new AdTagReport())
            ->setOpportunities(20)
            ->setImpressions(16)
            ->setPassbacks(4)
        ;

        $adTagReport4 = (new AdTagReport())
            ->setOpportunities(4)
            ->setImpressions(1)
            ->setPassbacks(3)
        ;

        $adSlotReport2 = (new AdSlotReport())
            ->setSlotOpportunities(22)
            ->addSubReport($adTagReport3)
            ->addSubReport($adTagReport4)
        ;

        // end ad slot

        $siteReport = (new SiteReport())
            ->addSubReport($adSlotReport1)
            ->addSubReport($adSlotReport2)
        ;

        $accountReport = (new AccountReport())
            ->addSubReport($siteReport)
        ;

        $platformReport = (new PlatformReport())
            ->addSubReport($accountReport)
        ;

        $platformReport->setCalculatedFields();

        $this->platformReport = $platformReport;
    }

    public function testPlatformReportTotalOpportunities()
    {
        $this->assertEquals(39, $this->platformReport->getTotalOpportunities());
    }

    public function testPlatformReportSlotOpportunities()
    {
        $this->assertEquals(32, $this->platformReport->getSlotOpportunities());
    }

    public function testPlatformReportImpressions()
    {
        $this->assertEquals(23, $this->platformReport->getImpressions());
    }

    public function testPlatformReportPassbacks()
    {
        $this->assertEquals(16, $this->platformReport->getPassbacks());
    }

    public function testPlatformReportFillRate()
    {
        $this->assertEquals(0.7188, $this->platformReport->getFillRate());
    }

    public function testAdTagFillRate()
    {
        $this->assertEquals(0.2, $this->adTagReport2->getFillRate());
    }

    public function testAdTagRelativeFillRate()
    {
        $this->assertEquals(0.1, $this->adTagReport2->getRelativeFillRate());
    }
}