<?php

namespace Tagcade\Service\Report\UnifiedReport\Exporter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\ComparisonReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison as ComparisonReportTypes;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network as NetworkReportTypes;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher as PublisherReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilder as UnifiedReportBuilder;

class ReportExporter implements ReportExporterInterface
{
    private $headers = ['Date', 'Name', 'Tagcade Opportunities', 'Requests', 'Opportunity Comparison', 'Tagcade Impressions', 'Partner Impressions','Tagcade Passbacks', 'Partner Passbacks', 'Passback Comparison',
        'Tagcade ECPM', 'Partner ECPM', 'ECPM Comparison', 'Tagcade Revenue', 'Partner Revenue','Revenue Opportunity', 'Tagcade Fill Rate', 'Partner Fill Rate'];

    /** @var UnifiedReportBuilder */
    protected $unifiedReportBuilder;

    /**
     * ReportExporter constructor.
     * @param UnifiedReportBuilder $unifiedReportBuilder
     */
    public function __construct(UnifiedReportBuilder $unifiedReportBuilder )
    {
        $this->unifiedReportBuilder = $unifiedReportBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByPartnerReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByDayReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getAllPartnersDiscrepancyByDayForPublisher($publisher, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersBySiteReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getAllPartnersDiscrepancyBySiteForPublisher($publisher, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByAdTagReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getAllPartnersDiscrepancyByAdTagForPublisher($publisher, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByAdTagReportForSubPublisher(SubPublisherInterface $publisher, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getPartnerAllSitesByDayReport(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getAllSitesDiscrepancyByDayForPartner($adNetwork, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getPartnerAllSitesByDayForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getPartnerByDayDiscrepancyReport($adNetwork, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getPartnerAllSitesBySitesReport(PublisherInterface $publisher, AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getPartnerAllSitesBySitesReport($publisher, $adNetwork, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getPartnerAllSitesByAdTagsReport(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getAllSitesDiscrepancyByAdTagForPartner($adNetwork, $params),
            $params
        );
    }

    public function getPartnerByAdTagsForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getAllSitesDiscrepancyByAdTagForPartnerWithSubPublisher($adNetwork, $subPublisher, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getPartnerSiteByAdTagsReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getSiteDiscrepancyByAdTagForPartner($adNetwork, $site, $params),
            $params
        );
    }

    public function getPartnerSiteByAdTagsForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getSiteDiscrepancyByAdTagForPartnerWithSubPublisher($adNetwork, $site, $subPublisher, $params),
            $params
        );
    }

    /**
     * @inheritdoc
     */
    public function getPartnerSiteByDaysReport(AdNetworkInterface $adNetwork, $domain, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getSiteDiscrepancyByDayForPartner($adNetwork, $domain, $params),
            $params
        );
    }

    public function getPartnerSiteByDaysForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, $domain, Params $params)
    {
        return $this->getResult(
            $this->unifiedReportBuilder->getSiteDiscrepancyByDayForPartner($adNetwork, $domain, $params),
            $params
        );
    }

    /**
     * get Result
     * @param ReportResultInterface $unifiedComparisonReports
     * @param Params $params
     * @return mixed
     */
    private function getResult(ReportResultInterface $unifiedComparisonReports, Params $params)
    {
        /** @var ReportResultInterface $unifiedReports */
        if (!$unifiedComparisonReports instanceof ReportResultInterface) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        if (!is_array($unifiedComparisonReports->getReports()) || count($unifiedComparisonReports->getReports()) < 1) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        $response = new Response();
        $response->headers->add(array (
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=data.csv'
        ));

        ob_start();
        $csv = fopen("php://output", 'w');
        fputcsv($csv, $this->headers);

        $subBreakDown = false;
        if (is_array($params->getQueryParams()) && $params->getQueryParams()['subBreakDown'] === true) {
            $subBreakDown = true;
        }

        /** @var ComparisonReportInterface $report */
        foreach($unifiedComparisonReports->getReports() as $report) {
            if ($subBreakDown === true && $report->getReports() !== null) {
                foreach($report->getReports() as $r) {
                    fputcsv($csv, $this->getReportDataArray($r));
                }
            } else {
                fputcsv($csv, $this->getReportDataArray($report));
            }

        }
        fclose($csv);
        $response->setContent(ob_get_clean());

        return $response;
    }

    /**
     * get Report Data Array from ReportDataInterface
     *
     * @param ComparisonReportInterface $comparisonReport
     * @return array|bool
     */
    private function getReportDataArray(ComparisonReportInterface $comparisonReport)
    {
        $date = $comparisonReport->getDate() ===  null ?
            sprintf('%s - %s', $comparisonReport->getStartDate()->format('M d, Y'), $comparisonReport->getEndDate()->format('M d, Y')) :
            $comparisonReport->getDate()->format('M d, Y');

        return [
            'date' => $date,
            'name' => $comparisonReport->getName(),
            'tagcadeOpportunities' => $comparisonReport->getTagcadeTotalOpportunities(),
            'requests' => $comparisonReport->getPartnerTotalOpportunities(),
            'opportunityComparison' => $this->getPercentageString($comparisonReport->getTotalOpportunityComparison()),
            'tagcadeImpressions' => $comparisonReport->getTagcadeImpressions(),
            'partnerImpressions' => $comparisonReport->getPartnerImpressions(),
            'tagcadePassbacks' => $comparisonReport->getTagcadePassbacks(),
            'partnerPassbacks' => $comparisonReport->getPartnerPassbacks(),
            'passbackComparison' => $this->getPercentageString($comparisonReport->getPassbacksComparison()),
            'tagcadeEcpm' => $comparisonReport->getTagcadeECPM(),
            'partnerEcpm' => $comparisonReport->getPartnerEstCPM(),
            'ecpmComparison' => $this->getPercentageString($comparisonReport->getECPMComparison()),
            'tagcadeRevenue' => $comparisonReport->getTagcadeEstRevenue(),
            'partnerRevenue' => $comparisonReport->getPartnerEstRevenue(),
            'revenueOpportunity' => $comparisonReport->getRevenueOpportunity(),
            'tagcadeFillRate' => $comparisonReport->getTagcadeFillRate(),
            'partnerFillRate' => $comparisonReport->getPartnerFillRate()
        ];
    }

    private function getPercentageString($ratio)
    {
        return sprintf('%d%%', round($ratio * 100));
    }

}