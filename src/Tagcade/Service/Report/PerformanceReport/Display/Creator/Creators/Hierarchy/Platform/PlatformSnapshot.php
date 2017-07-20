<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\PlatformReport;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class PlatformSnapshot extends SnapshotCreatorAbstract implements PlatformInterface, SnapshotCreatorInterface
{
    use CalculateRatiosTrait;
    use ConstructCalculatedReportTrait;

    const BILLED_AMOUNT = 'billed_amount';
    const BILLED_RATE = 'billed_rate';
    const IN_BANNER_BILLED_AMOUNT = 'inbanner_billed_amount';
    const IN_BANNER_BILLED_RATE = 'inbanner_billed_rate';

    /**
     * @var AccountSnapshot
     */
    private $accountSnapshotCreator;
    /**
     * @var PublisherManagerInterface
     */
    private $publisherManager;

    public function __construct(PublisherManagerInterface $publisherManager, AccountSnapshot $accountSnapshotCreator)
    {
        $this->accountSnapshotCreator = $accountSnapshotCreator;
        $this->publisherManager = $publisherManager;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        // not use reportType

        $report = new PlatformReport();
        $report
            ->setDate($this->getDate())
        ;

        $result = array(
            self::CACHE_KEY_SLOT_OPPORTUNITY => 0,
            self::CACHE_KEY_OPPORTUNITY => 0,
            self::CACHE_KEY_IMPRESSION => 0,
            self::CACHE_KEY_PASSBACK => 0,
            self::BILLED_AMOUNT => 0,
            self::IN_BANNER_BILLED_AMOUNT => 0,
            self::CACHE_KEY_IN_BANNER_REQUEST => 0,
            self::CACHE_KEY_IN_BANNER_IMPRESSION => 0,
            self::CACHE_KEY_IN_BANNER_TIMEOUT => 0,
        );

        $this->accountSnapshotCreator->setEventCounter($this->eventCounter);

        $this->logger->info('Getting all active publishers');
        $allPublishers = $this->publisherManager->allActivePublishers();

        $total = 0;
        $inBannerTotal = 0;
        foreach ($allPublishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }
            /**
             * @var AccountReport $accountReport
             */
            $this->logger->info(sprintf('Creating report for publisher %d', $publisher->getId()));
            $accountReport = $this->accountSnapshotCreator->createReport(new AccountReportType($publisher));

            $this->logger->info(sprintf('Finished report for publisher %d', $publisher->getId()));

            $result[self::CACHE_KEY_SLOT_OPPORTUNITY] += $accountReport->getSlotOpportunities();
            $result[self::CACHE_KEY_OPPORTUNITY] += $accountReport->getTotalOpportunities();
            $result[self::CACHE_KEY_IMPRESSION] += $accountReport->getImpressions();
            $result[self::CACHE_KEY_PASSBACK] += $accountReport->getPassbacks();
            $result[self::BILLED_AMOUNT] += $accountReport->getBilledAmount();
            $result[self::IN_BANNER_BILLED_AMOUNT] += $accountReport->getInBannerBilledAmount();
            $result[self::CACHE_KEY_IN_BANNER_REQUEST] += $accountReport->getInBannerRequests();
            $result[self::CACHE_KEY_IN_BANNER_IMPRESSION] += $accountReport->getInBannerImpressions();
            $result[self::CACHE_KEY_IN_BANNER_TIMEOUT] += $accountReport->getInBannerTimeouts();

            $total += $accountReport->getBilledRate() * $accountReport->getBilledAmount(); // for weighted value calculation latter
            $inBannerTotal += $accountReport->getInBannerBilledRate() * $accountReport->getInBannerBilledAmount(); // for in banner weighted value calculation latter

        }

        $this->logger->info('Finished getting all active publisher report');

        $billedRate = $this->getRatio($total, $result[self::BILLED_AMOUNT]); // weighted billed rate
        $inBannerBilledRate = $this->getRatio($inBannerTotal, $result[self::IN_BANNER_BILLED_AMOUNT]); // weighted billed rate
        $result[self::BILLED_RATE] = $billedRate == null ? 0 : $billedRate;
        $result[self::IN_BANNER_BILLED_RATE] = $inBannerBilledRate == null ? 0 : $inBannerBilledRate;

        $this->constructReportModel($report, $result);

        $this->logger->info('Finished constructing Platform report');

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PlatformReportType;
    }
}