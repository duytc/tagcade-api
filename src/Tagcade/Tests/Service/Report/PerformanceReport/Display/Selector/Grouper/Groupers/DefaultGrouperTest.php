<?php

namespace Tagcade\Tests\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\DefaultGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportCollection;

class DefaultGrouperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultGrouper $defaultGrouper
     */
    protected $defaultGrouper;

    public function setup()
    {
        $reportTypePlatform = new Platform([]);

        $platform1 = new PlatformReport();
        $platform1
            ->setDate(new DateTime('3 days ago'))
            ->setName('platform report 1')
            ->setTotalOpportunities(1000000)
            ->setPassbacks(100)
            ->setEstRevenue(150)
            ->setImpressions(1000000)
            ->setEstCpm(0.3)
        ;

        $platform2 = new PlatformReport();
        $platform2
            ->setDate(new DateTime('2 days ago'))
            ->setName('platform report 2')
            ->setTotalOpportunities(1100000)
            ->setPassbacks(10)
            ->setEstRevenue(500)
            ->setImpressions(1100000)
            ->setEstCpm(0.5)
        ;

        $platform3 = new PlatformReport();
        $platform3
            ->setDate(new DateTime('yesterday'))
            ->setName('platform report 3')
            ->setTotalOpportunities(1300000)
            ->setPassbacks(1000)
            ->setEstRevenue(900)
            ->setImpressions(1250000)
            ->setEstCpm(1)
        ;

        $reports = [
            $platform1,
            $platform2,
            $platform3,
        ];

        $reportResultCollection = new ReportCollection($reportTypePlatform , $platform1->getDate(), $platform3->getDate(), $reports);

        $defaultGrouper = new DefaultGrouper($reportResultCollection);

        $this->defaultGrouper = $defaultGrouper;
    }

    public function testDefaultGrouper()
    {
        $reportGroup = $this->defaultGrouper->getGroupedReport();

        $this->assertEquals($reportGroup->getFillRate(), 0.9853);
        $this->assertEquals($reportGroup->getEstCpm(), 0.771);

        $this->assertEquals($reportGroup->getAveragePassbacks(), 370);
        $this->assertEquals($reportGroup->getAverageEstCpm(), 0.6);
        $this->assertEquals($reportGroup->getAverageImpressions(), 1116667);
        $this->assertEquals($reportGroup->getAverageTotalOpportunities(), 1133333);
        $this->assertEquals($reportGroup->getAverageEstRevenue(), 516.6667);
    }
} 