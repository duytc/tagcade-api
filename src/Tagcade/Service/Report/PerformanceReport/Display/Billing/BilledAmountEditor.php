<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\GetRootReportTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;


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
     * @var PublisherManagerInterface
     */
    protected $userManager;
    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;

    /**
     * @var OutputInterface
     */
    protected $output;

    function __construct(
        ReportBuilderInterface $reportBuilder,
        BillingCalculatorInterface $billingCalculator,
        ObjectManager $om,
        CpmRateGetterInterface $rateGetter,
        PublisherManagerInterface $userManager,
        DateUtilInterface $dateUtil
    )
    {
        $this->reportBuilder     = $reportBuilder;
        $this->billingCalculator = $billingCalculator;
        $this->om                = $om;
        $this->rateGetter        = $rateGetter;
        $this->userManager       = $userManager;
        $this->dateUtil          = $dateUtil;
    }

    /**
     * @inheritdoc
     */
    public function updateHistoricalBilledAmount(PublisherInterface $publisher, $billingRate, DateTime $startDate, DateTime $endDate)
    {
        if( !is_numeric($billingRate) || $billingRate < 0) {
            throw new InvalidArgumentException('billing rate must be a float and positive number');
        }

        $yesterday = new DateTime('yesterday');

        if ($endDate > $yesterday) {
            $endDate = $yesterday;
        }

        return $this->doUpdateBilledAmountForPublisher($publisher,  new Params($startDate, $endDate), $billingRate);
    }

    public function updateBilledAmountThresholdForPublisher(PublisherInterface $publisher, DateTime $date = null)
    {
        if (null === $date) {
            $date = new DateTime('yesterday');
        }

        $today = new DateTime('today');

        if ($date >= $today) {
            return false; // nothing updated for first day of month, because update can only be done with yesterday of the same month
        }

        $params = new Params($this->dateUtil->getFirstDateInMonth($date), $this->dateUtil->getLastDateInMonth($date));

        if ($this->hasOutput()) {
            $this->output->writeln(sprintf("%s start updating billed amount for publisher '%s' from %s to %s\n",
                    date('c'), $publisher->getUser()->getUsername(), $params->getStartDate()->format('Y-m-d'), $params->getStartDate()->format('Y-m-d')));
        }

        $result = $this->doUpdateBilledAmountForPublisher($publisher, $params);

        if ($this->hasOutput()) {
            $this->output->writeln(sprintf("%s finish updating billed amount for publisher '%s' from %s to %s\n",
                    date('c'), $publisher->getUser()->getUsername(), $params->getStartDate()->format('Y-m-d'), $params->getStartDate()->format('Y-m-d')));
        }

        return $result;
    }

    public function updateBilledAmountThresholdForAllPublishers(DateTime $date = null)
    {
        if (null === $date) {
            $date = new DateTime('yesterday');
        }

        $today = new DateTime('today');
        $updatedPublisherCount = 0;

        if ($date >= $today) {
            return $updatedPublisherCount; // nothing updated for first day of month, because update can only be done with yesterday of the same month
        }

        $publishers = $this->userManager->allPublishers();

        foreach ($publishers as $publisher) {
            $updatedPublisherCount += $this->updateBilledAmountThresholdForPublisher($publisher, $date);
        }

        return $updatedPublisherCount;
    }

    /**
     * @param PublisherInterface $publisher
     * @param Params $param
     * @param string $billedRate If not set then we calculate rate base on publisher threshold
     * @return bool false on failure
     */
    protected function doUpdateBilledAmountForPublisher(PublisherInterface $publisher, Params $param, $billedRate = null)
    {
        $reportResult = $this->reportBuilder->getPublisherAdSlotsReport($publisher, $param);

        if (false === $reportResult) {
            return false;
        }

        $rootReports = [];

        gc_enable();

        /**
         * @var AdSlotReportInterface $reportRow
         * @var ReportResultInterface $report
         */
        foreach($reportResult->getReports() as $report) {
            foreach ($report->getReports() as $reportRow) {

                if (!$reportRow instanceof AdSlotReportInterface) {
                    throw new LogicException('expect AdSlotReportInterface');
                }

                if (!$this->shouldGetNewRate($reportRow, $billedRate)) {
                    continue;
                }

                $newCpmRate = $billedRate !== null ? $billedRate : $this->rateGetter->getThresholdRateForPublisher($publisher, $reportRow->getDate());

                if (round($reportRow->getBilledRate(), 4) === round($newCpmRate, 4)) { // not update if new rate is the same as current rate
                    continue;
                }

                $billedAmount = $this->billingCalculator->calculateBilledAmount($newCpmRate, $reportRow->getSlotOpportunities());
                $reportRow->setBilledAmount($billedAmount)
                          ->setBilledRate($newCpmRate)
                ;

                $root = $this->getRootReport($reportRow);

                if (!in_array($root, $rootReports, true)) {
                    $rootReports[] = $root;
                }

                unset($reportRow);
                unset($root);
            }
        }

        unset($report);
        unset($reportRow);

        // Step 2. update calculated fields from top level (Platform) to sub level (Account, Site, AdSlot,)
        foreach ($rootReports as $report) {
            /**
             * @var RootReportInterface $report
             */
            // very important, must be called manually because doctrine preUpdate listener doesn't work if changes happen in associated entities.
            if ($this->hasOutput()) {
                $this->output->writeln(sprintf("%s start updating billed amount for report on %s\n", date('c'), $report->getDate()->format('Y-m-d')));
            }
            /**
             * @var RootReportInterface $report
             */
            $report->setCalculatedFields();
            // Step 3. Update database
            $this->om->flush();

            $this->om->detach($report);

            if ($this->hasOutput()) {
                $this->output->writeln(sprintf("%s finish updating billed amount for report on %s\n", date('c'), $report->getDate()->format('Y-m-d')));
            }

            unset($report);

            gc_collect_cycles();

        }


        return true;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    private function hasOutput()
    {
        return null !== $this->output;
    }


    protected function shouldGetNewRate(AdSlotReportInterface $reportRow, $newRate = null) {
        if (null !== $newRate && $newRate !== $reportRow->getBilledRate()) { // wanna recalculate report with new rate
            return true;
        }
        else if (null === $newRate && $reportRow->getCustomRate() === null) { // should recalculate billed amount base on new threshold
            return true;
        }

        return false;
    }
}