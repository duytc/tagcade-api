<?php

namespace Tagcade\Tests\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculator;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\RateAmount;

class ProjectedBillingCalculatorTest extends \PHPUnit_Framework_TestCase
{

    protected $cpmRateGetterMock;
    protected $reportBuilderMock;
    protected $billingCalculatorMock;
    protected $dateUtilMock;

    protected $billedReportGroupMock;

    public function setup()
    {
        $cpmRateGetterMock = $this->getMock('Tagcade\Service\Report\PerformanceReport\Display\Billing\CpmRateGetterInterface');
        $cpmRateGetterMock->expects($this->once())
            ->method('getBilledRateForPublisher')
            ->will($this->returnValue(2));

        $reportBuilderMock = $this->getMock('Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface');

        $billingCalculatorMock = $this->getMock('Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface');
        $billingCalculatorMock->expects($this->once())
            ->method('calculateBilledAmount')
            ->will($this->returnValue(1000));

        $this->cpmRateGetterMock = $cpmRateGetterMock;
        $this->reportBuilderMock = $reportBuilderMock;
        $this->billingCalculatorMock = $billingCalculatorMock;

        $this->billedReportGroupMock = $this->_createBilledReportGroup();
    }

    public function testProjectedBilledAmountOnMidMonth()
    {
        $dateUtilMock = $this->_createDateUtil(6, 23);
        $projectedBilledAmount = $this->_getProjectedBilledAmount($dateUtilMock);

        $this->assertEquals(5000, $projectedBilledAmount);
    }

    public function testProjectedBilledAmountOnLastDayOfMonth()
    {
        $dateUtilMock = $this->_createDateUtil(29, 0);
        $projectedBilledAmount = $this->_getProjectedBilledAmount($dateUtilMock);

        $this->assertEquals(1034.4828, round($projectedBilledAmount, 4));
    }

//    public function testProjectedBilledAmountOnFirstDayOfMonth()
//    {
//        $dateUtilMock = $this->_createDateUtil(0, 29);
//        $projectedBilledAmount = $this->_getProjectedBilledAmount($dateUtilMock);
//
//       // $this->assertEquals(1034.4828, round($projectedBilledAmount, 4));
//    }

    private function _getProjectedBilledAmount($dateUtilMock)
    {
        $projectedBillingCalculator = new ProjectedBillingCalculator($this->cpmRateGetterMock, $this->reportBuilderMock, $this->billingCalculatorMock, $dateUtilMock);

        $methodToCall = new \ReflectionMethod('Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculator', 'getProjectedBilledAmount');
        $methodToCall->setAccessible(true);

        /**
         * @var RateAmount $projectedBilledAmount
         */
        $projectedBilledAmount = $methodToCall->invoke($projectedBillingCalculator, $this->billedReportGroupMock);

        return $projectedBilledAmount->getAmount();
    }

    private function _createBilledReportGroup($slotOpportunities=0)
    {
        $publisherMock = $this->getMock('Tagcade\Model\User\Role\PublisherInterface');
        $reportTypeAccount = new Account($publisherMock);

        $billedReportGroupMock = $this->getMockBuilder('Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup')
            ->disableOriginalConstructor()
            ->getMock();

        $billedReportGroupMock->expects($this->once())
            ->method('getReportType')
            ->will($this->returnValue($reportTypeAccount));

        $billedReportGroupMock->expects($this->any())
            ->method('getSlotOpportunities')
            ->will($this->returnValue($slotOpportunities));

        return $billedReportGroupMock;
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

        return $dateUtilMock;
    }
} 