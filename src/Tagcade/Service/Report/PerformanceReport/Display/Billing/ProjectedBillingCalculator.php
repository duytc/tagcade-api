<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as ReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;

class ProjectedBillingCalculator implements ProjectedBillingCalculatorInterface
{
    /**
     * @var ReportBuilderInterface
     */
    protected $reportBuilder;
    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;
    /**
     * @var BillingCalculatorInterface
     */
    protected $billingCalculator;

    function __construct(ReportBuilderInterface $reportBuilder, BillingCalculatorInterface $billingCalculator, DateUtilInterface $dateUtil)
    {
        $this->reportBuilder = $reportBuilder;
        $this->billingCalculator = $billingCalculator;
        $this->dateUtil = $dateUtil;
    }

    public function calculateProjectedBilledAmountForPublisher(PublisherInterface $publisher)
    {
        /**
         * @var BilledReportGroup $reportGroup
         */
        $params = $this->_createProjectedParam();
        if (false === $params) {
            return false;
        }

        $reportGroup = $this->reportBuilder->getPublisherReport($publisher, $params);

        return $this->getProjectedBilledAmount($reportGroup);
    }

    protected function getProjectedBilledAmount(BilledReportGroup $reportGroup)
    {
        $reportType = $reportGroup->getReportType();

        if (!$reportType instanceof ReportTypes\Account) {
            throw new InvalidArgumentException('Expected calculated report of type account');
        }

        //$billedAmountUpToYesterday  = $this->billingCalculator->calculateBilledAmountForPublisher($reportType->getPublisher(), $reportGroup->getSlotOpportunities())->getAmount();
        $billedAmountUpToYesterday  = 1111; // sum from database
        $dayAverageBilledAmount = $billedAmountUpToYesterday / $this->dateUtil->getNumberOfDatesPassedInMonth();
        $projectedBilledAmount  = $billedAmountUpToYesterday + ($dayAverageBilledAmount * ($this->dateUtil->getNumberOfRemainingDatesInMonth() + 1)) ; // +1 to include today

        return $projectedBilledAmount;
    }

    private function _createProjectedParam()
    {
        if ($this->dateUtil->isFirstDateOfMonth()) {
            return false;
        }

        $params     = new Params($this->dateUtil->getFirstDateInMonth(), new DateTime('yesterday'));
        $params->setGrouped(true);

        return $params;
    }
} 