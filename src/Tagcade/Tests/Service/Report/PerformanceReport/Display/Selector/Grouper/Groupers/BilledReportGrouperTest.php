<?php

namespace Tagcade\Tests\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\BilledReportGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\DefaultGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportCollection;

class BilledReportGrouperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BilledReportGrouper $billedReportGrouper
     */
    protected $billedReportGrouper;

    public function setup()
    {
        $reportTypePlatform = new Platform([]);

        $platform1 = new PlatformReport();
        $platform1
            ->setDate(new DateTime('3 days ago'))
            ->setName('platform report 1')
            ->setSlotOpportunities(1000000)
            ->setTotalOpportunities(1000000)
            ->setPassbacks(100)
            ->setEstRevenue(150)
            ->setImpressions(1000000)
            ->setEstCpm(0.3)
            ->setBilledAmount(6)
        ;

        $platform2 = new PlatformReport();
        $platform2
            ->setDate(new DateTime('2 days ago'))
            ->setName('platform report 2')
            ->setSlotOpportunities(1100000)
            ->setTotalOpportunities(1100000)
            ->setPassbacks(10)
            ->setEstRevenue(500)
            ->setImpressions(1100000)
            ->setEstCpm(0.5)
            ->setBilledAmount(8)
        ;

        $platform3 = new PlatformReport();
        $platform3
            ->setDate(new DateTime('yesterday'))
            ->setName('platform report 3')
            ->setSlotOpportunities(1300000)
            ->setTotalOpportunities(1300000)
            ->setPassbacks(1000)
            ->setEstRevenue(900)
            ->setImpressions(1250000)
            ->setEstCpm(1)
            ->setBilledAmount(10)
        ;

        $reports = [
            $platform1,
            $platform2,
            $platform3,
        ];

        $reportResultCollection = new ReportCollection($reportTypePlatform , $platform1->getDate(), $platform3->getDate(), $reports);

        $billedReportGrouper = new BilledReportGrouper($reportResultCollection);

        $this->billedReportGrouper = $billedReportGrouper;
    }

    public function testBilledReportGrouper()
    {
        $billedReportGroup = $this->billedReportGrouper->getGroupedReport();

        $this->assertEquals($billedReportGroup->getFillRate(), 0.9853);
        $this->assertEquals($billedReportGroup->getEstCpm(), 0.771);
//
        $this->assertEquals($billedReportGroup->getAveragePassbacks(), 370);
        $this->assertEquals($billedReportGroup->getAverageEstCpm(), 0.6);
        $this->assertEquals($billedReportGroup->getAverageImpressions(), 1116667);
        $this->assertEquals($billedReportGroup->getAverageTotalOpportunities(), 1133333);
        $this->assertEquals($billedReportGroup->getAverageEstRevenue(), 516.6667);

        $this->assertEquals($billedReportGroup->getBilledAmount(), 24);
    }
} 