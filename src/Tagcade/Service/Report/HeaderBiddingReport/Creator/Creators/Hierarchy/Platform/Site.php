<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform\SiteReport;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Site as SiteReportType;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\HasSubReportsTrait;

class Site extends CreatorAbstract implements SiteInterface
{
    use HasSubReportsTrait;

    /** @var AdSlotRepositoryInterface */
    private $adSlotRepository;

    public function __construct(AdSlotInterface $subReportCreator, AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->subReportCreator = $subReportCreator;
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(SiteReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new SiteReport();

        $site = $reportType->getSite();

        $report
            ->setSite($site)
            ->setDate($this->getDate());

        $allAdSlots = $this->adSlotRepository->getAdSlotsForSite($site);

        foreach ($allAdSlots as $adSlot) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new AdSlotReportType($adSlot))
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }
}