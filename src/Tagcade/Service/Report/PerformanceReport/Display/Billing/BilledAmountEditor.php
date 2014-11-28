<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use DateTime;
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
    private $reportBuilder;
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var BillingCalculatorInterface
     */
    private $billingCalculator;

    function __construct(ReportBuilderInterface $reportBuilder, BillingCalculatorInterface $billingCalculator, ObjectManager $om)
    {
        $this->reportBuilder    = $reportBuilder;
        $this->billingCalculator = $billingCalculator;
        $this->om                = $om;
    }

    /**
     * @inheritdoc
     */
    public function updateBilledAmountForPublisher(PublisherInterface $publisher, $billingRate, DateTime $startDate, DateTime $endDate)
    {
        if( !is_numeric($billingRate) || $billingRate < 0) {
            throw new InvalidArgumentException('billing rate must be a float and positive number');
        }

        $today = new DateTime('today');

        if(!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate >= $today || $endDate >= $today ) {
            throw new InvalidArgumentException('Can only update billed amount information for reports older than today');
        }

        $param = new Params($startDate, $endDate);
        $reports = $this->reportBuilder->getPublisherAdSlotsReport($publisher, $param);

        /**
         * @var AdSlotReportInterface $reportRow
         */
        $publisher->getUser()->setBillingRate($billingRate);

        $rootReports = [];

        foreach($reports as $report) {
            foreach ($report->getReports() as $reportRow) {

                $newBilledAmount = $this->billingCalculator->calculateBilledAmountForPublisher($publisher, $reportRow->getSlotOpportunities());
                $reportRow->setBilledAmount($newBilledAmount);

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

        return $this;
    }
} 