<?php

namespace Tagcade\Tests\Model\Report\PerformanceReport\Display;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork as AdNetworkReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    protected $adTagReport1;
    protected $adTagReport2;
    /**
     * @var PlatformReportTypes\PlatformReport
     */
    protected $platformReport;
    protected $adNetworkReport;

    public function setUp()
    {
        $adTagReport1 = (new PlatformReportTypes\AdTagReport())
            ->setTotalOpportunities(10)
            ->setImpressions(5)
            ->setPassbacks(5)
            ->setPosition(1)
            ->setEstCpm(400)
        ;

        $this->adTagReport1 = $adTagReport1;

        $adTagReport2 = (new PlatformReportTypes\AdTagReport())
            ->setTotalOpportunities(5)
            ->setImpressions(1)
            ->setPassbacks(4)
            ->setPosition(2)
            ->setEstCpm(1000)
        ;

        $this->adTagReport2 = $adTagReport2;

        $adSlotReport1 = (new PlatformReportTypes\AdSlotReport())
            ->setSlotOpportunities(10)
            ->addSubReport($adTagReport1)
            ->addSubReport($adTagReport2)
        ;

        // end first ad slot

        $adTagReport3 = (new PlatformReportTypes\AdTagReport())
            ->setTotalOpportunities(20)
            ->setImpressions(16)
            ->setPassbacks(4)
            ->setPosition(1)
            ->setEstCpm(100)
        ;

        $adTagReport4 = (new PlatformReportTypes\AdTagReport())
            ->setTotalOpportunities(4)
            ->setImpressions(1)
            ->setPassbacks(3)
            ->setPosition(2)
            ->setEstCpm(1000)
        ;

        $adSlotReport2 = (new PlatformReportTypes\AdSlotReport())
            ->setSlotOpportunities(22)
            ->addSubReport($adTagReport3)
            ->addSubReport($adTagReport4)
        ;

        // end ad slot

        $siteReport = (new PlatformReportTypes\SiteReport())
            ->addSubReport($adSlotReport1)
            ->addSubReport($adSlotReport2)
        ;

        $accountReport = (new PlatformReportTypes\AccountReport())
            ->addSubReport($siteReport)
        ;

        $platformReport = (new PlatformReportTypes\PlatformReport())
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

    public function testAdTagReportFillRate()
    {
        $this->assertEquals(0.2, $this->adTagReport2->getFillRate());
    }

    public function testAdTagReportRelativeFillRate()
    {
        $this->assertEquals(0.1, $this->adTagReport2->getRelativeFillRate());
    }

    public function testPlatformEstRevenue()
    {
        $this->assertEquals(5.6, $this->platformReport->getEstRevenue());
    }

    public function testAdSlotEstCpm()
    {
        /**
         * @var PlatformReportTypes\AccountReportInterface $account
         */
        $accountReport = $this->platformReport->getSubReports()[0];

        /**
         * @var PlatformReportTypes\SiteReport
         */
        $siteReport = $accountReport->getSubReports()[0];


        /**
         * @var PlatformReportTypes\AdSlotReportInterface $adSlotReport1
         */
        $adSlotReport1 = $siteReport->getSubReports()[0];

        /**
         * @var PlatformReportTypes\AdSlotReportInterface
         */
        $adSlotReport2 = $siteReport->getSubReports()[1];


        $this->assertEquals(600, $adSlotReport1->getEstCpm());

        $this->assertEquals(446.1538, round($adSlotReport2->getEstCpm(), 4));

    }

    public function testSiteEstCpm()
    {
        /**
         * @var PlatformReportTypes\AccountReportInterface $account
         */
        $accountReport = $this->platformReport->getSubReports()[0];

        /**
         * @var PlatformReportTypes\SiteReport
         */
        $siteReport = $accountReport->getSubReports()[0];

        $this->assertEquals(528.5714, round($siteReport->getEstCpm(), 4));
    }

    public function testPlatformEstCpm()
    {
        $this->assertEquals(528.5714, round($this->platformReport->getEstCpm(), 4));
    }
}