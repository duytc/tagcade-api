<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\GetRootReportTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;


class BilledAmountEditor implements BilledAmountEditorInterface
{
    use GetRootReportTrait;
    /**
     * @var ReportSelectorInterface
     */
    protected  $reportBuilder;
    /**
     * @var ObjectManager
     */
    protected $om;
    /**
     * @var BillingCalculatorInterface
     */
    protected $billingCalculator;
    /**
     * @var CpmRateGetterInterface
     */
    protected $rateGetter;
    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;

    function __construct(
        ReportBuilderInterface $reportBuilder,
        BillingCalculatorInterface $billingCalculator,
        ObjectManager $om,
        CpmRateGetterInterface $rateGetter,
        DateUtilInterface $dateUtil
    )
    {
        $this->reportBuilder     = $reportBuilder;
        $this->billingCalculator = $billingCalculator;
        $this->om                = $om;
        $this->rateGetter        = $rateGetter;
        $this->dateUtil          = $dateUtil;
    }

    /**
     * @inheritdoc
     */
    public function updateBilledAmountForPublisher(PublisherInterface $publisher, $billingRate, DateTime $startDate, DateTime $endDate)
    {
        /**
         * @var AdSlotReportInterface $reportRow
         */
        $this->doUpdateBilledAmountForPublisher($publisher, $billingRate, $startDate, $endDate);
        $publisher->getUser()->setBillingRate($billingRate); // set custom rate for publisher

        return $this;
    }


    public function updateBilledAmountToCurrentDateForPublisher(PublisherInterface $publisher)
    {
        $param = new Params($this->dateUtil->getFirstDateOfMonth(), new DateTime('today'));
        $param->setGrouped(true);
        /**
         * @var CalculatedReportGroup $reportGroup
         */
        $reportGroup = $this->reportBuilder->getPublisherReport($publisher, $param);
        $newBilledRate = $this->rateGetter->getBilledRateForPublisher($publisher, $reportGroup->getSlotOpportunities());
        $lastRate = $this->rateGetter->getLastRateForPublisher($publisher);

        if ($lastRate != $newBilledRate) {
            // TODO set last rate for publisher then do update billedAmount
            $this->doUpdateBilledAmountForPublisher($publisher, $newBilledRate, $param->getStartDate(), $param->getEndDate());
        }

        return $this;
    }

    protected function doUpdateBilledAmountForPublisher(PublisherInterface $publisher, $billedRate, $startDate, $endDate)
    {
        if( !is_numeric($billedRate) || $billedRate < 0) {
            throw new InvalidArgumentException('billing rate must be a float and positive number');
        }

        if(!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate > $endDate) {
            throw new InvalidArgumentException('Start date should be less than or equal end date');
        }

        $today = new DateTime('today');

        if ($startDate >= $today || $endDate >= $today ) {
            throw new InvalidArgumentException('Can only update billed amount information for reports older than today');
        }

        $param = new Params($startDate, $endDate);
        $reports = $this->reportBuilder->getPublisherAdSlotsReport($publisher, $param);

        $rootReports = [];

        /**
         * @var CalculatedReportInterface $reportRow
         */
        foreach($reports as $report) {
            foreach ($report->getReports() as $reportRow) {
                $billedAmount = $this->billingCalculator->calculateBilledAmount($billedRate, $reportRow->getSlotOpportunities());
                $reportRow->setBilledAmount($billedAmount);
                $reportRow->setBilledRate($billedRate);
                $root = $this->getRootReport($reportRow);
                if (!in_array($root, $rootReports, true)) {
                    $rootReports[] = $root;
                }

                unset($root);
            }
        }

        unset($report);
        unset($reportRow);

        // Step 2. update calculated fields from top level (Platform) to sub level (Account, Site, AdSlot,)
        foreach ($rootReports as $report) {
            // very important, must be called manually because doctrine preUpdate listener doesn't work if changes happen in associated entities.
            /**
             * @var RootReportInterface $report
             */
            $report->setCalculatedFields();
        }

        // Step 3. Update database
        $this->om->flush();
    }

} 