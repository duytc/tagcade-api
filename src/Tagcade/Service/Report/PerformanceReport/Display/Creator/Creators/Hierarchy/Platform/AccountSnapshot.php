<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AccountReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class AccountSnapshot extends BillableSnapshotCreatorAbstract implements AccountInterface, SnapshotCreatorInterface
{
    use HasSubReportsTrait;

    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

    public function __construct(AdSlotManagerInterface $adSlotManager, AdTagManagerInterface $adTagManager, BillingCalculatorInterface $billingCalculator)
    {
        parent::__construct($billingCalculator);

        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AccountReportType $reportType)
    {
        $report = new AccountReport();
        $publisher = $reportType->getPublisher();
        $publisherName = $publisher->getCompany();
        $report
            ->setPublisher($publisher)
            ->setName($publisherName === null ? $publisher->getUser()->getUsername() : $publisherName)
            ->setDate($this->getDate())
        ;

        $reportableAdSlotIds = $this->adSlotManager->getReportableAdSlotIdsForPublisher($publisher);
        $adSlotReportCounts = $this->eventCounter->getAdSlotReports($reportableAdSlotIds);
        unset($reportableAdSlotIds);

        $adTagIdsForPublisher = $this->adTagManager->getActiveAdTagsIdsForPublisher($publisher);
        $adTagReportCounts =  $this->eventCounter->getAdTagReports($adTagIdsForPublisher);
        unset($adTagIdsForPublisher);

        $this->parseRawReportData($report, array_merge($adSlotReportCounts, $adTagReportCounts));

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountReportType;
    }

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof CalculatedReportInterface) {
            throw new InvalidArgumentException('Expect CalculatedReportInterface');
        }

        $report->setSlotOpportunities($data[self::CACHE_KEY_SLOT_OPPORTUNITY])
            ->setTotalOpportunities($data[self::CACHE_KEY_OPPORTUNITY])
            ->setImpressions($data[self::CACHE_KEY_IMPRESSION])
            ->setPassbacks($data[self::CACHE_KEY_PASSBACK])
            ->setFillRate()

        ;
        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}