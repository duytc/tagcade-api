<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Bundle\UserBundle\Entity\User as AbstractUser;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\DataType\CpmRate;
use Tagcade\Service\Report\PerformanceReport\Display\GetRootReportTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;

class BilledAmountEditor implements BilledAmountEditorInterface
{
    use GetRootReportTrait;

    /** @var ReportSelectorInterface */
    protected $reportBuilder;

    /** @var ObjectManager */
    protected $om;

    /** @var BillingCalculatorInterface */
    protected $billingCalculator;

    /** @var CpmRateGetterInterface */
    protected $rateGetter;

    /** @var PublisherManagerInterface */
    protected $userManager;

    /** @var AccountReportRepositoryInterface */
    protected $accountReportRepository;

    /** @var DateUtilInterface */
    protected $dateUtil;

    /** @var LoggerInterface */
    protected $logger;

    /** @var AdSlotReportRepositoryInterface */
    protected $adSlotReportRepository;

    function __construct(
        ReportBuilderInterface $reportBuilder,
        BillingCalculatorInterface $billingCalculator,
        ObjectManager $om,
        CpmRateGetterInterface $rateGetter,
        PublisherManagerInterface $userManager,
        DateUtilInterface $dateUtil,
        AdSlotReportRepositoryInterface $adSlotReportRepository,
        AccountReportRepositoryInterface $accountReportRepository
    )
    {
        $this->reportBuilder = $reportBuilder;
        $this->billingCalculator = $billingCalculator;
        $this->om = $om;
        $this->rateGetter = $rateGetter;
        $this->userManager = $userManager;
        $this->dateUtil = $dateUtil;
        $this->adSlotReportRepository = $adSlotReportRepository;
        $this->accountReportRepository = $accountReportRepository;
    }

    /**
     * @inheritdoc
     */
    public function updateHistoricalBilledAmount(PublisherInterface $publisher, $billingRate, DateTime $startDate, DateTime $endDate)
    {
        if (!is_numeric($billingRate) || $billingRate < 0) {
            throw new InvalidArgumentException('billing rate must be a float and positive number');
        }

        $yesterday = new DateTime('yesterday');

        if ($endDate > $yesterday) {
            $endDate = $yesterday;
        }

        return $this->doUpdateBilledAmountForPublisher($publisher, new Params($startDate, $endDate), $billingRate);
    }

    public function updateBilledAmountThresholdForPublisher(PublisherInterface $publisher, DateTime $date = null)
    {
        if (null === $date) {
            $date = new DateTime('yesterday');
        }

        $today = new DateTime('today');
        $today->setTime(23, 59, 59);

        if ($date > $today) {
            return false; // nothing updated for first day of month, because update can only be done with yesterday of the same month
        }

        $params = new Params($this->dateUtil->getFirstDateInMonth($date), $this->dateUtil->getLastDateInMonth($date));


        $this->writeln(sprintf("start updating billed amount for publisher '%s' from %s to %s",
            $publisher->getUser()->getUsername(), $params->getStartDate()->format('Y-m-d'), $params->getEndDate()->format('Y-m-d')));

        $result = $this->doUpdateBilledAmountForPublisher($publisher, $params);

        $this->writeln(sprintf("finish updating billed amount for publisher '%s' from %s to %s",
            $publisher->getUser()->getUsername(), $params->getStartDate()->format('Y-m-d'), $params->getEndDate()->format('Y-m-d')));


        return $result;
    }

    public function updateBilledAmountThresholdForAllPublishers(DateTime $date = null)
    {
        if (null === $date) {
            $date = new DateTime('yesterday');
        }

        $today = new DateTime('today');
        $today->setTime(23, 59, 59);
        $updatedPublisherCount = 0;

        if ($date > $today) {
            return $updatedPublisherCount; // nothing updated for first day of month, because update can only be done with yesterday of the same month
        }

        $params = new Params($this->dateUtil->getFirstDateInMonth($date), $this->dateUtil->getLastDateInMonth($date));

        $this->writeln("getting all ad slot reports for the date range");

        $reportResult = $this->adSlotReportRepository->getAllReportInRange($params->getStartDate(), $params->getEndDate());

        if (count($reportResult) < 1) {
            return false;
        }

        $this->writeln("finished getting the ad slot reports");

        return $this->handleUpdateBilledAmountForAdSlotReports($reportResult);
    }

    /**
     * @param PublisherInterface $publisher
     * @param Params $param
     * @param string $billedRate If not set then we calculate rate base on publisher threshold
     * @return bool false on failure
     */
    protected function doUpdateBilledAmountForPublisher(PublisherInterface $publisher, Params $param, $billedRate = null)
    {
        $reportResult = $this->adSlotReportRepository->getAllReportInRangeForPublisher($publisher, $param->getStartDate(), $param->getEndDate());

        if (false === $reportResult) {
            return false;
        }

        return $this->handleUpdateBilledAmountForAdSlotReports($reportResult, $billedRate);
    }

    /**
     * Update billed amount with custom billed rate if specified. Otherwise the calculation is done via threshold billed rate
     * @param array $reportResult
     * @param float|null $billedRate
     * @return bool
     */
    protected function handleUpdateBilledAmountForAdSlotReports(array $reportResult, $billedRate = null)
    {
        $rootReports = $this->getRootReportsFromAdSlotReports($reportResult, $billedRate);
        unset($reportResult);

        foreach ($rootReports as $key => $rootReport) {
            if ($rootReport instanceof CalculatedReportInterface) {
                $this->updateBilledAmount($rootReport);
            }

            $this->writeln(sprintf("finished detaching entities for report on %s", $rootReport->getDate()->format('Y-m-d')));

            $rootReports[$key] = null;
            $rootReport = null;
            gc_collect_cycles();
        }

        unset($rootReports);


        return true;
    }

    /**
     * @param array $reportResult
     * @param float|null $billedRate
     * @return array
     */
    protected function getRootReportsFromAdSlotReports(array $reportResult, $billedRate = null)
    {
        $rootReports = [];

        /**
         * @var AdSlotReportInterface $reportRow
         */
        foreach ($reportResult as $reportRow) {

            if (!$reportRow instanceof AdSlotReportInterface) {
                throw new LogicException('expect AdSlotReportInterface');
            }

            if (!$this->shouldGetNewRate($reportRow, $billedRate)) {
                continue;
            }

            $adSlot = $reportRow->getAdSlot();
            if (!$adSlot instanceof BaseAdSlotInterface) {
                $this->logger->warning(sprintf('Ad slot not found in AdSlot report %', $reportRow->getId()));
                continue;
            }

            $publisher = $reportRow->getAdSlot()->getSite()->getPublisher();

            /** @var CpmRate $newCpmRate */
            $newCpmRate = $billedRate !== null ? new CpmRate($newCpmRate, true) : $this->rateGetter->getCpmRateForPublisherByMonth($publisher, AbstractUser::MODULE_DISPLAY, $reportRow->getDate());

            if (round($reportRow->getBilledRate(), 4) === round($newCpmRate->getCpmRate(), 4)) { // not update if new rate is the same as current rate
                continue;
            }

            $billedAmount = $this->billingCalculator->calculateBilledAmount($newCpmRate->getCpmRate(), $reportRow->getSlotOpportunities());
            $reportRow->setBilledAmount($billedAmount)
                ->setBilledRate($newCpmRate->getCpmRate());

            $root = $this->getRootReport($reportRow);

            if (!in_array($root, $rootReports, true)) {
                $rootReports[] = $root;
            }

            unset($reportRow);
            unset($root);
        }

        unset($reportRow);

        return $rootReports;
    }

    protected function updateBilledAmount(CalculatedReportInterface $report)
    {
        /**
         * @var CalculatedReportInterface|RootReportInterface| $report
         */
        // very important, must be called manually because doctrine preUpdate listener doesn't work if changes happen in associated entities.
        $this->writeln(sprintf("start updating billed amount for report on %s", $report->getDate()->format('Y-m-d')));
        /**
         * @var CalculatedReportInterface|RootReportInterface $report
         */
        $report->setThresholdBilledAmount();

        // Step 3. Update database
        $this->om->flush();

        $this->writeln(sprintf("finish updating billed amount for report on %s", $report->getDate()->format('Y-m-d')));
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    protected function writeln($line)
    {
        if ($this->hasLogger()) {
            $this->logger->info($line);
        }
    }

    /**
     * flush Then Detach
     *
     * @param $entities
     */
    protected function flushThenDetach($entities)
    {
        $this->om->flush();

        $this->detach($entities);
    }

    protected function detach($entities)
    {
        $myEntities = is_array($entities) ? $entities : [$entities];

        foreach ($myEntities as $entity) {
            $tmp = is_array($entity) ? $entity : [$entity];

            foreach ($tmp as $e) {
                $this->om->detach($e);
            }
        }
    }

    private function hasLogger()
    {
        return null !== $this->logger;
    }

    protected function shouldGetNewRate(AdSlotReportInterface $reportRow, $newRate = null)
    {
        if (null !== $newRate && $newRate !== $reportRow->getBilledRate()) { // wanna recalculate report with new rate
            return true;
        } else if (null === $newRate && $reportRow->getCustomRate() === null) { // should recalculate billed amount base on new threshold
            return true;
        }

        return false;
    }
}