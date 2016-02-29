<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Report\RtbReport\AccountReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\RtbReport\Hierarchy\SiteReport;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Account as AccountReportType;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Site as SiteReportType;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorAbstract;

class RtbAccountSnapshot extends RtbSnapshotCreatorAbstract implements RtbAccountSnapshotInterface
{
    /**
     * @var RtbSiteSnapshot
     */
    private $rtbSiteSnapshotCreator;

    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    public function __construct(RtbSiteSnapshot $rtbSiteSnapshotCreator, SiteManagerInterface $siteManager)
    {
        $this->rtbSiteSnapshotCreator = $rtbSiteSnapshotCreator;
        $this->siteManager = $siteManager;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AccountReportType $reportType)
    {
        $accountReport = new AccountReport();

        $publisher = $reportType->getPublisher();
        $publisherName = $publisher->getCompany();

        $accountReport
            ->setPublisher($publisher)
            ->setName($publisherName === null ? $publisher->getUser()->getUsername() : $publisherName)
            ->setDate($this->getDate());

        $sites = $this->siteManager->getSitesForPublisher($publisher);

        $result = array(
            self::RESULT_KEY_SLOT_OPPORTUNITY => 0,
            self::RESULT_KEY_IMPRESSION => 0,
            self::RESULT_KEY_PRICE => 0,
        );

        $this->rtbSiteSnapshotCreator->setEventCounter($this->eventCounter);

        foreach ($sites as $site) {
            /** @var SiteReport $siteReport */
            $siteReport = $this->rtbSiteSnapshotCreator->createReport(new SiteReportType($site));
            $result[self::RESULT_KEY_SLOT_OPPORTUNITY] += $siteReport->getOpportunities();
            $result[self::RESULT_KEY_IMPRESSION] += $siteReport->getImpressions();
            $result[self::RESULT_KEY_PRICE] += $siteReport->getEarnedAmount();

            $accountReport->addSubReport($siteReport->setSuperReport($accountReport));
        }

        $this->constructReportModel($accountReport, $result);

        return $accountReport;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountReportType;
    }

    /**
     * @inheritdoc
     */
    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof AccountReport) {
            throw new InvalidArgumentException('Expect instance AccountReport');
        }

        $report
            ->setOpportunities($data[self::RESULT_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[self::RESULT_KEY_IMPRESSION])
            ->setEarnedAmount($data[self::RESULT_KEY_PRICE])
            ->setFillRate();
    }
}