<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\PlatformReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class PlatformSnapshot extends SnapshotCreatorAbstract implements PlatformInterface, SnapshotCreatorInterface
{
    use CalculateRatiosTrait;

    const BILLED_AMOUNT = 'billed_amount';
    const BILLED_RATE = 'billed_rate';
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

    public function doCreateReport(PlatformReportType $reportType)
    {
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
        );

        $this->accountSnapshotCreator->setEventCounter($this->eventCounter);
        $allPublishers = $this->publisherManager->allActivePublishers();

        $total = 0;
        foreach ($allPublishers as $publisher) {
            /**
             * @var AccountReport $accountReport
             */
            $accountReport = $this->accountSnapshotCreator->createReport(new AccountReportType($publisher));
            $result[self::CACHE_KEY_SLOT_OPPORTUNITY] += $accountReport->getSlotOpportunities();
            $result[self::CACHE_KEY_OPPORTUNITY] += $accountReport->getTotalOpportunities();
            $result[self::CACHE_KEY_IMPRESSION] += $accountReport->getImpressions();
            $result[self::CACHE_KEY_PASSBACK] += $accountReport->getPassbacks();
            $result[self::BILLED_AMOUNT] += $accountReport->getBilledAmount();

            $total += $accountReport->getBilledRate() * $accountReport->getBilledAmount(); // for weighted value calculation latter

        }

        $result[self::BILLED_RATE] = $this->getRatio($total, $result[self::BILLED_AMOUNT]); // weighted billed rate

        $this->constructReportModel($report, $result);

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PlatformReportType;
    }


    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof PlatformReport) {
            throw new InvalidArgumentException('Expect PlatformReport');
        }

        $report->setSlotOpportunities($data[self::CACHE_KEY_SLOT_OPPORTUNITY])
            ->setTotalOpportunities($data[self::CACHE_KEY_OPPORTUNITY])
            ->setImpressions($data[self::CACHE_KEY_IMPRESSION])
            ->setPassbacks($data[self::CACHE_KEY_PASSBACK])
            ->setFillRate()
            ->setBilledAmount($data[self::BILLED_AMOUNT])
            ->setBilledRate($data[self::BILLED_RATE])

        ;
        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}