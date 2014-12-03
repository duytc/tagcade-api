<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as ReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\DateUtil;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;

class ProjectedBillingCalculator extends BillingCalculator implements ProjectedBillingCalculatorInterface
{
    /**
     * @var ReportBuilderInterface
     */
    protected $reportBuilder;
    /**
     * @var DateUtilInterface
     */
    private $dateUtil;

    function __construct($defaultCpmRate = 0.0025, array $defaultBilledThresholds = [], ReportBuilderInterface $reportBuilder, DateUtil $dateUtil)
    {
        parent::__construct($defaultCpmRate, $defaultBilledThresholds);

        $this->reportBuilder = $reportBuilder;
        $this->dateUtil = $dateUtil;
    }

    public function calculateProjectedBilledAmountForAllPublishers()
    {
        $params  = $this->_createProjectedParam();
        /**
         * @var CalculatedReportGroup $reportGroup
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
         * @var CalculatedReportGroup $reportGroup
         */
        $params = $this->_createProjectedParam();
        $reportGroup = $this->reportBuilder->getPublisherReport($publisher, $params);

        return $this->getProjectedBilledAmount($reportGroup, $params);
    }

    protected function getProjectedBilledAmount(CalculatedReportGroup $reportGroup)
    {
        $reportType = $reportGroup->getReportType();

        if (!$reportType instanceof ReportTypes\Account) {
            throw new InvalidArgumentException('Expected calculated report of type account');
        }

        $cpmRate = $this->getCustomCpmRateForPublisher($reportType->getPublisher());

        if (null !== $cpmRate) {
            return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate, $reportGroup->getSlotOpportunities()));
        }

        $cpmRate                = $this->findDefaultCpmRate($reportGroup->getSlotOpportunities());
        $billedAmountUpToToday  = $this->calculateBilledAmount($cpmRate, $reportGroup->getSlotOpportunities());
        $dayAverageBilledAmount = $billedAmountUpToToday / $this->dateUtil->getNumberOfDatesUpToToday();
        $projectedBilledAmount  = $billedAmountUpToToday + ($dayAverageBilledAmount * $this->dateUtil->getNumberOfRemainingDatesOfMonth()) ;

        return new RateAmount($cpmRate, $projectedBilledAmount);
    }

    private function _createProjectedParam()
    {
        $params     = new Params($this->dateUtil->getFirstDateOfMonth(), new DateTime('today'));
        $params->setGrouped(true);

        return $params;
    }
} 