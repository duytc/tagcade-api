<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractReport as AbstractTagcadeReport;
use Tagcade\Model\Report\UnifiedReport\Comparison\AbstractReport as AbstractUnifiedComparisonReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilder as TagcadeReportBuilder;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilder as UnifiedReportBuilder;

/**
 * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
 *
 * Only allow admins and publishers with the display module enabled
 */
class UnifiedReportExportController extends FOSRestController
{
    const EXPORT_DIR = '/public/export/report/unifiedReport';
    const EXPORT_TYPE_UNIFIED_REPORT = 'unifiedReport';
    const EXPORT_TYPE_UNIFIED_COMPARISON_REPORT = 'unifiedComparisonReport';
    const EXPORT_TYPE_TAGCADE_REPORT = 'tagcadePartnerReport';

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

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners/all/partners", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getPublisherAllPartnersByPartnerAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $params = $this->getParams();

        /** @var ReportResultInterface $unifiedReports */
        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        if (!$unifiedReports instanceof ReportResultInterface) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        /** @var ReportResultInterface $unifiedReports */
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        if (!$unifiedComparisonReports instanceof ReportResultInterface) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        /** @var ReportResultInterface $unifiedReports */
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);
        if (!$tagcadePartnerReports instanceof ReportResultInterface) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports, $params
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getPublisherAllPartnersByDayAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $params = $this->getParams();

        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners/all/sites", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $publisherId
     *
     * @return array
     */
    public function getPublisherAllPartnersBySiteAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $params = $this->getParams();

        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners/all/adtags", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $publisherId
     *
     * @return array
     */
    public function getPublisherAllPartnersByAdTagAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $params = $this->getParams();

        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners/{partnerId}/sites", requirements={"publisherId" = "\d+", "partnerId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $publisherId
     * @param $partnerId
     * @return array
     */
    public function getPartnerAllSitesByDayAction($publisherId, $partnerId)
    {
        $publisher = $this->getPublisher($publisherId);
        $partner = $this->getAdNetworkHasPartnerWithPublisher($partnerId, $publisher);
        $params = $this->getParams();

        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners/{partnerId}/sites/all/sites", requirements={"publisherId" = "\d+", "partnerId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $publisherId
     *
     * @param $partnerId
     * @return array
     */
    public function getPartnerAllSiteBySiteAction($publisherId, $partnerId)
    {
        $publisher = $this->getPublisher($publisherId);
        $partner = $this->getAdNetworkHasPartnerWithPublisher($partnerId, $publisher);
        $params = $this->getParams();

        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners/{partnerId}/sites/all/adtags", requirements={"publisherId" = "\d+", "partnerId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $publisherId
     *
     * @param $partnerId
     * @return array
     */
    public function getPartnerAllSitesByAdTagAction($publisherId, $partnerId)
    {
        $publisher = $this->getPublisher($publisherId);
        $partner = $this->getAdNetworkHasPartnerWithPublisher($partnerId, $publisher);
        $params = $this->getParams();

        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners/{partnerId}/sites/{siteId}", requirements={"publisherId" = "\d+", "partnerId" = "\d+", "siteId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $publisherId
     * @param $partnerId
     * @param $siteId
     * @return array
     */
    public function getPartnerSiteByDayAction($publisherId, $partnerId, $siteId)
    {
        $publisher = $this->getPublisher($publisherId);
        $partner = $this->getAdNetworkHasPartnerWithPublisher($partnerId, $publisher);
        $site = $this->getSite($siteId);
        $params = $this->getParams();

        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}/partners/{partnerId}/sites/{siteId}/adtags", requirements={"publisherId" = "\d+", "partnerId" = "\d+", "siteId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $publisherId
     *
     * @param $partnerId
     * @param $siteId
     * @return array
     */
    public function getPartnerSiteByAdTagAction($publisherId, $partnerId, $siteId)
    {
        $publisher = $this->getPublisher($publisherId);
        $adNetwork = $this->getAdNetworkHasPartnerWithPublisher($partnerId, $publisher);
        $site = $this->getSite($siteId);
        $params = $this->getParams();

        $unifiedReports = $this->getUnifiedReportBuilder()->getAllDemandPartnersByPartnerReport($publisher, $params);
        $unifiedComparisonReports = $this->getUnifiedReportBuilder()->getAllPartnersDiscrepancyByPartnerForPublisher($publisher, $params);
        $tagcadePartnerReports = $this->getTagcadeReportBuilder()->getAllPartnersReportByPartnerForPublisher($publisher, $params);

        return $this->getResult(
            $unifiedReports, $unifiedComparisonReports, $tagcadePartnerReports
        );
    }

    /* === private function === */
    /**
     * @param $publisherId
     * @throws LogicException
     * @return PublisherInterface
     */
    protected function getPublisher($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            // try again with SubPublisher
            $publisher = $this->get('tagcade_user_system_sub_publisher.user_manager')->findUserBy(array('id' => $publisherId));
        }

        if (!$publisher instanceof PublisherInterface) {
            throw new LogicException('The user should have the publisher role');
        }

        $this->checkUserPermission($publisher);

        return $publisher;
    }

    /**
     * @param int $adNetworkId
     * @return \Tagcade\Model\Core\AdNetworkInterface
     */
    protected function getAdNetwork($adNetworkId)
    {
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($adNetworkId);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That ad network does not exist');
        }

        $this->checkUserPermission($adNetwork);

        return $adNetwork;
    }

    /**
     * @param int $siteId
     * @return \Tagcade\Model\Core\SiteInterface
     */
    protected function getSite($siteId)
    {
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        $this->checkUserPermission($site);

        return $site;
    }

    /**
     * get ad network by $adNetworkId that has a partner with publisher
     * @param int $adNetworkId
     * @param PublisherInterface $publisher
     * @return \Tagcade\Model\Core\AdNetworkInterface
     */
    protected function getAdNetworkHasPartnerWithPublisher($adNetworkId, PublisherInterface $publisher)
    {
        $adNetwork = $this->getAdNetwork($adNetworkId);
        $publisherId = ($publisher instanceof SubPublisherInterface) ? $publisher->getPublisher()->getId() : $publisher->getId();

        if (!$adNetwork->getNetworkPartner() instanceof AdNetworkPartnerInterface
            || $adNetwork->getPublisher()->getId() != $publisherId
        ) {
            throw new NotFoundHttpException('That ad network does not have partner with publisher');
        }

        return $adNetwork;
    }

    /**
     * @param mixed $entity The entity instance
     * @return bool
     * @throws AccessDeniedException
     */
    protected function checkUserPermission($entity)
    {
        $securityContext = $this->get('security.context');

        // allow admins to everything
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // check voters
        if (false === $securityContext->isGranted('view', $entity)) {
            throw new AccessDeniedException(sprintf('You do not have permission to view this'));
        }

        return true;
    }

    /**
     * @return Params
     */
    private function getParams()
    {
        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        return $this->_createParams($params);
    }

    /**
     * @var array $params
     * @return Params
     */
    private function _createParams(array $params)
    {
        // create a params array with all values set to null
        $defaultParams = array_fill_keys([
            Params::PARAM_START_DATE,
            Params::PARAM_END_DATE,
            Params::PARAM_EXPAND,
            Params::PARAM_GROUP
        ], null);

        $params = array_merge($defaultParams, $params);

        $dateUtil = $this->get('tagcade.service.date_util');
        $startDate = $dateUtil->getDateTime($params[Params::PARAM_START_DATE], true);
        $endDate = $dateUtil->getDateTime($params[Params::PARAM_END_DATE]);

        $expanded = filter_var($params[Params::PARAM_EXPAND], FILTER_VALIDATE_BOOLEAN);
        $grouped = filter_var($params[Params::PARAM_GROUP], FILTER_VALIDATE_BOOLEAN);

        return new Params($startDate, $endDate, $expanded, $grouped);
    }

    /**
     * @return UnifiedReportBuilder
     */
    private function getUnifiedReportBuilder()
    {
        return $this->get('tagcade.service.report.unified_report.selector.report_builder');
    }

    /**
     * @return TagcadeReportBuilder
     */
    private function getTagcadeReportBuilder()
    {
        return $this->get('tagcade.service.report.performance_report.display.selector.report_builder');
    }

    /**
     * get Result
     * @param ReportResultInterface $unifiedReports
     * @param ReportResultInterface $unifiedComparisonReports
     * @param ReportResultInterface $tagcadePartnerReports
     * @param Params $params
     * @return mixed
     */
    private function getResult(ReportResultInterface $unifiedReports, ReportResultInterface $unifiedComparisonReports, ReportResultInterface $tagcadePartnerReports, Params $params)
    {
        if (!is_array($unifiedReports->getReports()) || count($unifiedReports->getReports()) < 1
            || !is_array($unifiedComparisonReports->getReports()) || count($unifiedComparisonReports->getReports()) < 1
            || !is_array($tagcadePartnerReports->getReports()) || count($tagcadePartnerReports->getReports()) < 1
        ) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        // create csv and get real file path on api server
        $exportedFilePaths = [
            $this->createCsvFile($unifiedReports->getReports(), self::EXPORT_TYPE_UNIFIED_REPORT, $params),
            $this->createCsvFile($unifiedComparisonReports->getReports(), self::EXPORT_TYPE_UNIFIED_COMPARISON_REPORT, $params),
            $this->createCsvFile($tagcadePartnerReports->getReports(), self::EXPORT_TYPE_TAGCADE_REPORT, $params),
        ];

        return $exportedFilePaths;
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
        $realFilePath = __DIR__ . '/../../../../../web' . $filePath;

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

        return sprintf('%s/%s', self::EXPORT_DIR, $fileName);
    }

    /**
     * get Report Data Array from ReportDataInterface
     *
     * @param ReportDataInterface $reportData
     * @param $exportType
     * @return array|bool
     */
    private function getReportDataArray(ReportDataInterface $reportData, $exportType)
    {
        //if ($reportData instanceof AbstractUnifiedReport) {
        if (self::EXPORT_TYPE_UNIFIED_REPORT == $exportType) {
            // requests, impressions, passbacks, revenue, cpm, fillRate
            return [
                'requests' => $reportData->getTotalOpportunities(),
                'impressions' => $reportData->getImpressions(),
                'passbacks' => $reportData->getPassbacks(),
                'revenue' => $reportData->getEstRevenue(),
                'cpm' => $reportData->getEstCpm(),
                'fillRate' => $reportData->getFillRate()
            ];
        }

        //if ($reportData instanceof AbstractUnifiedComparisonReport) {
        if (self::EXPORT_TYPE_UNIFIED_COMPARISON_REPORT == $exportType) {
            // tagcadeOpportunities, requests, opportunityComparison, tagcadePassbacks, partnerPassbacks, passbackComparison, tagcadeEcpm, partnerEcpm, ecpmComparison, revenueOpportunity
            /** @var AbstractUnifiedComparisonReport $reportData */
            return [
                'tagcadeOpportunities' => $reportData->getTagcadeTotalOpportunities(),
                'requests' => $reportData->getTotalOpportunities(),
                'opportunityComparison' => $reportData->getTotalOpportunityComparison(),
                'tagcadePassbacks' => $reportData->getTagcadePassbacks(),
                'partnerPassbacks' => $reportData->getPartnerPassbacks(),
                'passbackComparison' => $reportData->getPassbacksComparison(),
                'tagcadeEcpm' => $reportData->getTagcadeECPM(),
                'partnerEcpm' => $reportData->getPartnerEstCPM(),
                'ecpmComparison' => $reportData->getECPMComparison(),
                'revenueOpportunity' => $reportData->getRevenueOpportunity()
            ];
        }

        // Important: AbstractTagcadeReport is base AbstractReport, so we must check at the end
        //if ($reportData instanceof AbstractTagcadeReport) {
        if (self::EXPORT_TYPE_TAGCADE_REPORT == $exportType) {
            // networkOpportunities, impressions, passbacks, fillRate
            return [
                'networkOpportunities' => $reportData->getTotalOpportunities(),
                'impressions' => $reportData->getImpressions(),
                'passbacks' => $reportData->getPassbacks(),
                'fillRate' => $reportData->getFillRate()
            ];
        }

        return false;
    }
}
