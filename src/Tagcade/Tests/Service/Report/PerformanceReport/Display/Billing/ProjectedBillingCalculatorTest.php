<?php

namespace Tagcade\Tests\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculator;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\RateAmount;

class ProjectedBillingCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    public function setup()
    {
        $this->publisher = $this->getMock('Tagcade\Model\User\Role\PublisherInterface');
    }

    public function testProjectedBilledAmountOnMidMonth()
    {
        $dateUtilMock = $this->_createDateUtil(6, 23);

        $projectedBillingCalculator = $this->_initCalculator($dateUtilMock);
        $projectedBilling = $projectedBillingCalculator->calculateProjectedBilledAmountForPublisher($this->publisher);

        $this->assertEquals(5000, $projectedBilling);
    }

    public function testProjectedBilledAmountOnLastDayOfMonth()
    {
        $dateUtilMock = $this->_createDateUtil(29, 0);
        $projectedBillingCalculator = $this->_initCalculator($dateUtilMock);
        $projectedBilling = $projectedBillingCalculator->calculateProjectedBilledAmountForPublisher($this->publisher);
        $this->assertEquals(1034.4828, round($projectedBilling, 4));
    }

    private function _initCalculator(DateUtilInterface $dateUtil)
    {
        $accountReportRepositoryMock = $this->getMock('Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface');
        $accountReportRepositoryMock->expects($this->once())
            ->method('getSumBilledAmountForPublisher')
            ->will($this->returnValue(1000))
        ;

        return new ProjectedBillingCalculator($accountReportRepositoryMock, $dateUtil);
    }

    private function _createDateUtil($daysPassed, $daysRemaining)
    {
        $dateUtilMock = $this->getMock('Tagcade\Service\DateUtilInterface');
        $dateUtilMock->expects($this->once())
            ->method('getNumberOfDatesPassedInMonth')
            ->will($this->returnValue($daysPassed));

        $dateUtilMock->expects($this->once())
            ->method('getNumberOfRemainingDatesInMonth')
            ->will($this->returnValue($daysRemaining));

        $dateUtilMock->expects($this->any())
            ->method('getFirstDateInMonth')
            ->will($this->returnValue(new \DateTime('yesterday')));

        return $dateUtilMock;
    }
} 