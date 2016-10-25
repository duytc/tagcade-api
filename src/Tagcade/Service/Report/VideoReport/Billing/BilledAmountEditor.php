<?php


namespace Tagcade\Service\Report\VideoReport\Billing;



use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Tagcade\Bundle\UserBundle\Entity\User as AbstractUser;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\Platform\VideoWaterfallTagReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\CalculatedReportInterface as VideoCalculatedReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\Behaviors\CalculateBilledAmountTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\DataType\CpmRate;
use Tagcade\Service\Report\PerformanceReport\Display\GetRootReportTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Model\Report\VideoReport\RootReportInterface as VideoRootReportInterface;
use Tagcade\Model\Report\VideoReport\SubReportInterface as VideoSubReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface as VideoReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;

class BilledAmountEditor implements BilledAmountEditorInterface
{
    use GetRootReportTrait;
    use CalculateBilledAmountTrait;

    /** @var DateUtilInterface */
    protected $dateUtil;

    /** @var ObjectManager */
    protected $om;

    /** @var LoggerInterface */
    protected $logger;

    /** @var CpmRateGetterInterface */
    protected $rateGetter;

    /** @var VideoWaterfallTagReportRepositoryInterface */
    protected $waterfallTagReportRepository;

    /**
     * BilledAmountEditor constructor.
     * @param DateUtilInterface $dateUtil
     * @param ObjectManager $om
     * @param LoggerInterface $logger
     * @param CpmRateGetterInterface $rateGetter
     * @param VideoWaterfallTagReportRepositoryInterface $waterfallTagReportRepository
     */
    public function __construct(DateUtilInterface $dateUtil, ObjectManager $om, LoggerInterface $logger, CpmRateGetterInterface $rateGetter, VideoWaterfallTagReportRepositoryInterface $waterfallTagReportRepository)
    {
        $this->dateUtil = $dateUtil;
        $this->om = $om;
        $this->logger = $logger;
        $this->rateGetter = $rateGetter;
        $this->waterfallTagReportRepository = $waterfallTagReportRepository;
    }


    /**
     * @param DateTime|null $date
     * @return mixed
     */
    public function updateVideoBilledAmountThresholdForAllPublishers(DateTime $date = null)
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

        $reportResult = $this->waterfallTagReportRepository->getReportInRangeForAllPublisher($params->getStartDate(), $params->getEndDate());

        if (count($reportResult) < 1) {
            return false;
        }

        $this->writeln("finished getting the ad slot reports");

        return $this->handleUpdateBilledAmountForWaterfallTagReports($reportResult);
    }

    /**
     * @param PublisherInterface $publisher
     * @param DateTime|null $date
     * @return mixed
     */
    public function updateVideoBilledAmountThresholdForPublisher(PublisherInterface $publisher, DateTime $date = null)
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

        $result = $this->doUpdateVideoBilledAmountForPublisher($publisher, $params);

        $this->writeln(sprintf("finish updating billed amount for publisher '%s' from %s to %s",
            $publisher->getUser()->getUsername(), $params->getStartDate()->format('Y-m-d'), $params->getEndDate()->format('Y-m-d')));


        return $result;
    }

    /**
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }


    /**
     * @param PublisherInterface $publisher
     * @param Params $param
     * @param string $billedRate If not set then we calculate rate base on publisher threshold
     * @return bool false on failure
     */
    protected function doUpdateVideoBilledAmountForPublisher(PublisherInterface $publisher, Params $param, $billedRate = null)
    {
        $reportResult = $this->waterfallTagReportRepository->getReportInRangeForPublisher($publisher, $param->getStartDate(), $param->getEndDate());

        if (false === $reportResult) {
            return false;
        }

        return $this->handleUpdateBilledAmountForWaterfallTagReports($reportResult, $billedRate);
    }


    /**
     * Update billed amount with custom billed rate if specified. Otherwise the calculation is done via threshold billed rate
     * @param array $reportResult
     * @param float|null $billedRate
     * @return bool
     */
    protected function handleUpdateBilledAmountForWaterfallTagReports(array $reportResult, $billedRate = null)
    {
        $rootReports = $this->getRootReportsFromWaterfallTagReports($reportResult, $billedRate);
        unset($reportResult);

        foreach ($rootReports as $key => $rootReport) {
            if ($rootReport instanceof VideoCalculatedReportInterface) {
                $this->updateVideoBilledAmount($rootReport);
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
    protected function getRootReportsFromWaterfallTagReports(array $reportResult, $billedRate = null)
    {
        $rootReports = [];

        /**
         * @var WaterfallTagReportInterface $reportRow
         */
        foreach ($reportResult as $reportRow) {

            if (!$reportRow instanceof WaterfallTagReportInterface) {
                throw new LogicException('expect WaterfallTagReportInterface');
            }

            if (!$this->shouldGetNewRate($reportRow, $billedRate)) {
                continue;
            }

            $waterfallTag = $reportRow->getVideoWaterfallTag();
            if (!$waterfallTag instanceof VideoWaterfallTagInterface) {
                $this->logger->warning(sprintf('Waterfall Tag not found in WaterfallTag report %d', $reportRow->getId()));
                continue;
            }

            $publisher = $waterfallTag->getVideoPublisher()->getPublisher();

            /** @var CpmRate $newCpmRate */
            $newCpmRate = $billedRate !== null ? new CpmRate($newCpmRate, true) : $this->rateGetter->getCpmRateForPublisherByMonth($publisher, AbstractUser::MODULE_VIDEO, $reportRow->getDate());

            if (round($reportRow->getBilledRate(), 4) === round($newCpmRate->getCpmRate(), 4)) { // not update if new rate is the same as current rate
                continue;
            }

            $billedAmount = $this->calculateBilledAmount($newCpmRate->getCpmRate(), $reportRow->getImpressions());
            $reportRow->setBilledAmount($billedAmount)
                ->setBilledRate($newCpmRate->getCpmRate());

            $root = $this->getVideoRootReport($reportRow);

            if (!in_array($root, $rootReports, true)) {
                $rootReports[] = $root;
            }

            unset($reportRow);
            unset($root);
        }

        unset($reportRow);

        return $rootReports;
    }

    /**
     * @param VideoReportInterface $report
     * @return RootReportInterface;
     */
    protected function getVideoRootReport(VideoReportInterface $report)
    {
        if (!$report instanceof VideoSubReportInterface) {
            return $report; // the report is root itself
        }

        $current = $report;

        // Loop 10 times to prevent infinite loop due to programming mistake
        for($i = 0; $i < 10; $i ++) {
            if (!$current instanceof VideoSubReportInterface) {
                throw new LogicException('Expected SubReportInterface');
            }

            $current = $current->getSuperReport();

            if($current instanceof VideoRootReportInterface) {
                break;
            }
        }

        if(!$current instanceof VideoRootReportInterface) {
            throw new LogicException('Expected RootReportInterface');
        }

        return $current;
    }

    protected function writeln($line)
    {
        if ($this->hasLogger()) {
            $this->logger->info($line);
        }
    }

    protected function updateVideoBilledAmount(VideoCalculatedReportInterface $report)
    {
        /**
         * @var VideoCalculatedReportInterface|VideoRootReportInterface| $report
         */
        // very important, must be called manually because doctrine preUpdate listener doesn't work if changes happen in associated entities.
        $this->writeln(sprintf("start updating billed amount for report on %s", $report->getDate()->format('Y-m-d')));
        /**
         * @var VideoCalculatedReportInterface|VideoRootReportInterface $report
         */
        $report->setThresholdBilledAmount();

        // Step 3. Update database
        $this->om->flush();

        $this->writeln(sprintf("finish updating billed amount for report on %s", $report->getDate()->format('Y-m-d')));
    }

    private function hasLogger()
    {
        return null !== $this->logger;
    }

    protected function shouldGetNewRate($reportRow, $newRate = null)
    {
        if (null !== $newRate && $newRate !== $reportRow->getBilledRate()) { // wanna recalculate report with new rate
            return true;
        } else if (null === $newRate && $reportRow->getCustomRate() === null) { // should recalculate billed amount base on new threshold
            return true;
        }

        return false;
    }
}