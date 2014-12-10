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
     * @var CpmRateGetterInterface
     */
    protected $rateGetter;
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

    function __construct(CpmRateGetterInterface $rateGetter, ReportBuilderInterface $reportBuilder, BillingCalculatorInterface $billingCalculator, DateUtilInterface $dateUtil)
    {
        $this->rateGetter = $rateGetter;
        $this->reportBuilder = $reportBuilder;
        $this->billingCalculator = $billingCalculator;
        $this->dateUtil = $dateUtil;
    }

    public function calculateProjectedBilledAmountForAllPublishers()
    {
        $params  = $this->_createProjectedParam();
        /**
         * @var BilledReportGroup $reportGroup
         */
        $reportGroups = $this->reportBuilder->getAllPublishersReport($params);
        $rateAmounts = [];

        foreach ($reportGroups as $reportGroup) {
            $rateAmounts[] = $this->getProjectedBilledAmount($reportGroup, $params);
        }

        return $rateAmounts;
    }

    public function calculateProjectedBilledAmountForPublisher(PublisherInterface $publisher)
    {
        /**
         * @var BilledReportGroup $reportGroup
         */
        $params = $this->_createProjectedParam();
        $reportGroup = $this->reportBuilder->getPublisherReport($publisher, $params);

        return $this->getProjectedBilledAmount($reportGroup);
    }

    protected function getProjectedBilledAmount(BilledReportGroup $reportGroup)
    {
        $reportType = $reportGroup->getReportType();

        if (!$reportType instanceof ReportTypes\Account) {
            throw new InvalidArgumentException('Expected calculated report of type account');
        }

        $cpmRate = $this->rateGetter->getBilledRateForPublisher($reportType->getPublisher(), $reportGroup->getSlotOpportunities());
        $billedAmountUpToYesterday  = $this->billingCalculator->calculateBilledAmount($cpmRate, $reportGroup->getSlotOpportunities());
        $dayAverageBilledAmount = $billedAmountUpToYesterday / $this->dateUtil->getNumberOfDatesPassedInMonth();
        $projectedBilledAmount  = $billedAmountUpToYesterday + ($dayAverageBilledAmount * ($this->dateUtil->getNumberOfRemainingDatesInMonth() + 1)) ; // +1 to include today

        return new RateAmount($cpmRate, $projectedBilledAmount);
    }

    private function _createProjectedParam()
    {
        if ($this->dateUtil->isFirstDateOfMonth()) {
            throw new RuntimeException('Projected billed amount cannot be calculated on the first day of month'); // projected billed amount can only be computed if we have yesterday data.
        }

        $params     = new Params($this->dateUtil->getFirstDateOfMonth(), new DateTime('yesterday'));
        $params->setGrouped(true);

        return $params;
    }
} 