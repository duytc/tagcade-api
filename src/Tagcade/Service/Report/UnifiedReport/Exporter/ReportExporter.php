<?php

namespace Tagcade\Service\Report\UnifiedReport\Exporter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\ComparisonReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison as ComparisonReportTypes;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network as NetworkReportTypes;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher as PublisherReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilder as TagcadeReportBuilder;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilder as UnifiedReportBuilder;

class ReportExporter implements ReportExporterInterface
{
    const EXPORT_DIR_DEFAULT = '/public/export/report/unifiedReport';
    const EXPORT_TYPE_UNIFIED_REPORT = 'unifiedReport';
    const EXPORT_TYPE_UNIFIED_COMPARISON_REPORT = 'unifiedComparisonReport';
    const EXPORT_TYPE_TAGCADE_REPORT = 'tagcadePartnerReport';

    private $headers = ['Date', 'Name', 'Tagcade Opportunities', 'Requests', 'Opportunity Comparison', 'Tagcade Impressions', 'Partner Impressions','Tagcade Passbacks', 'Partner Passbacks', 'Passback Comparison',
        'Tagcade ECPM', 'Partner ECPM', 'ECPM Comparison', 'Tagcade Revenue', 'Partner Revenue','Revenue Opportunity', 'Tagcade Fill Rate', 'Partner Fill Rate'];

    private static $CSV_HEADER_MAP = array(
        self::EXPORT_TYPE_UNIFIED_REPORT => ['Requests', 'Impressions', 'Passbacks', 'Revenue', 'CPM', 'Fill Rate'],
        self::EXPORT_TYPE_TAGCADE_REPORT => ['Network Opportunities', 'Impressions', 'Passbacks', 'Fill Rate'],
        self::EXPORT_TYPE_UNIFIED_COMPARISON_REPORT => ['Tagcade Opportunities', 'Requests', 'Opportunity Comparison', 'Tagcade Passbacks', 'Partner Passbacks', 'Passback Comparison', 'Tagcade ECPM', 'Partner ECPM', 'ECPM Comparison', 'Revenue Opportunity']
    );

    private static $REPORT_FIELDS_EXPORT_MAP = array(
        self::EXPORT_TYPE_UNIFIED_REPORT => ['requests', 'impressions', 'passbacks', 'revenue', 'cpm', 'fillRate'],
        self::EXPORT_TYPE_TAGCADE_REPORT => ['networkOpportunities', 'impressions', 'passbacks', 'fillRate'],
        self::EXPORT_TYPE_UNIFIED_COMPARISON_REPORT => ['tagcadeOpportunities', 'requests', 'opportunityComparison', 'tagcadePassbacks', 'partnerPassbacks', 'passbackComparison', 'tagcadeEcpm', 'partnerEcpm', 'ecpmComparison', 'revenueOpportunity']
    );

    /** @var UnifiedReportBuilder */
    protected $unifiedReportBuilder;

    /** @var TagcadeReportBuilder */
    protected $tagcadeReportBuilder;

    /** @var string we need to inject the root dir of the application to remove "up dir (../)" action */
    protected $__rootDir__;

    /** @var string */
    protected $exportDir;

    /**
     * @param UnifiedReportBuilder $unifiedReportBuilder
     * @param TagcadeReportBuilder $tagcadeReportBuilder
     * @param string $rootDir the root dir of application
     * @param $exportDir
     */
    public function __construct(
        UnifiedReportBuilder $unifiedReportBuilder,
        TagcadeReportBuilder $tagcadeReportBuilder,
        $rootDir, $exportDir
    )
    {
        $this->unifiedReportBuilder = $unifiedReportBuilder;
        $this->tagcadeReportBuilder = $tagcadeReportBuilder;
        $this->__rootDir__ = $rootDir;

        $this->exportDir = (null == $exportDir || '' == $exportDir) ? self::EXPORT_DIR_DEFAULT : $exportDir;
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
            $this->unifiedReportBuilder->getPartnerByDayDiscrepancyReport($adNetwork, $params),
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
            $this->unifiedReportBuilder->getAllDemandPartnersBySiteDiscrepancyReport($publisher, $adNetwork, $params),
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
    private function getResult($unifiedComparisonReports, Params $params)
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

        /** @var ComparisonReportInterface $report */
        foreach($unifiedComparisonReports->getReports() as $report) {
            fputcsv($csv, $this->getReportDataArray($report));
        }
        fclose($csv);
        $response->setContent(ob_get_clean());

        return $response;
    }

    /**
     * create csv and get real file path on api server
     *
     * @param array|ReportResultInterface[] $reportData
     * @param string $exportType
     * @param Params $params
     * @return string
     */
    private function createCsvFile(array $reportData, $exportType, Params $params)
    {
        if (!array_key_exists($exportType, self::$CSV_HEADER_MAP)) {
            throw new RuntimeException('Could not export report to file');
        }

        // create file and return path
        $filePath = $this->getReportFilePath($exportType, $params->getStartDate(), $params->getEndDate());
        $realFilePath = $this->__rootDir__ . '/../web' . $filePath;

        $handle = fopen($realFilePath, 'w+');

        if (!$handle) {
            throw new RuntimeException('Could not export report to file');
        }

        try {
            // write header row
            fputcsv($handle, self::$CSV_HEADER_MAP[$exportType]);

            // write all report data rows
            for ($i = 0, $len = count($reportData); $i < $len; $i++) {
                $reportData_i = $reportData[$i];

                if (!$reportData_i instanceof ReportDataInterface) {
                    continue;
                }

                // convert to array
                $reportDataArray = $this->getReportDataArray($reportData_i, $exportType);

                if (!is_array($reportDataArray)) {
                    continue;
                }

                // mapping reportData_i to rowData
                $rowData = array_map(function ($field) use ($reportDataArray) {
                    return array_key_exists($field, $reportDataArray) ? $reportDataArray[$field] : '';
                }, self::$REPORT_FIELDS_EXPORT_MAP[$exportType]);

                // append rowData to file
                fputcsv($handle, $rowData);
            }

            fclose($handle);
        } catch (\Exception $e) {
            throw new RuntimeException('Could not export report to file: ' . $e);
        }

        return $filePath;
    }

    /**
     * get Report File Path
     *
     * @param $exportType
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return bool|string
     */
    private function getReportFilePath($exportType, \DateTime $startDate, \DateTime $endDate)
    {
        $format = '%s - %s - %s.csv'; // <startDate Y-m-d> - <endDate Y-m-d> - <exportType mapping name>

        $mappedName = false;

        if (self::EXPORT_TYPE_UNIFIED_REPORT == $exportType) {
            $mappedName = 'unifiedReport';
        } else if (self::EXPORT_TYPE_UNIFIED_COMPARISON_REPORT == $exportType) {
            $mappedName = 'unifiedComparisonReport';
        } else if (self::EXPORT_TYPE_TAGCADE_REPORT == $exportType) {
            $mappedName = 'tagcadeReport';
        }

        if (!$mappedName) {
            return false;
        }

        $fileName = sprintf($format, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $mappedName);

        return sprintf('%s/%s', $this->exportDir, $fileName);
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
            'opportunityComparison' => $comparisonReport->getTotalOpportunityComparison(),
            'tagcadeImpressions' => $comparisonReport->getTagcadeImpressions(),
            'partnerImpressions' => $comparisonReport->getPartnerImpressions(),
            'tagcadePassbacks' => $comparisonReport->getTagcadePassbacks(),
            'partnerPassbacks' => $comparisonReport->getPartnerPassbacks(),
            'passbackComparison' => $comparisonReport->getPassbacksComparison(),
            'tagcadeEcpm' => $comparisonReport->getTagcadeECPM(),
            'partnerEcpm' => $comparisonReport->getPartnerEstCPM(),
            'ecpmComparison' => $comparisonReport->getECPMComparison(),
            'tagcadeRevenue' => $comparisonReport->getTagcadeEstRevenue(),
            'partnerRevenue' => $comparisonReport->getPartnerEstRevenue(),
            'revenueOpportunity' => $comparisonReport->getRevenueOpportunity(),
            'tagcadeFillRate' => $comparisonReport->getTagcadeFillRate(),
            'partnerFillRate' => $comparisonReport->getPartnerFillRate()
        ];
    }
}