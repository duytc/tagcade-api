<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilder as UnifiedReportBuilder;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilder as TagcadeReportBuilder;

/**
 * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
 *
 * Only allow admins and publishers with the display module enabled
 */
class UnifiedReportExportController extends FOSRestController
{
    const PUBLISHER_KEY = 'publisher';
    const AD_NETWORK_KEY = 'adNetwork';

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

        return $this->getResult(
            $this->getReportBuilder()->getAllPartnersDiscrepancyByDayForPublisher($publisher, $this->getParams())
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

        return $this->getResult(
            $this->getReportBuilder()->getAllPartnersDiscrepancyBySiteForPublisher($publisher, $this->getParams())
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

        return $this->getResult(
            $this->getReportBuilder()->getAllPartnersDiscrepancyByAdTagForPublisher($publisher, $this->getParams())
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

        return $this->getResult(
            $publisher instanceof SubPublisherInterface
                ? $this->getReportBuilder()->getAllSitesDiscrepancyByDayForPartnerWithSubPublisher($partner, $publisher, $this->getParams())
                : $this->getReportBuilder()->getAllSitesDiscrepancyByDayForPartner($partner, $this->getParams())
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

        return $this->getResult(
            $publisher instanceof SubPublisherInterface
                ? $this->getReportBuilder()->getAllSitesDiscrepancyBySiteForPartnerWithSubPublisher($partner, $publisher, $this->getParams())
                : $this->getReportBuilder()->getAllSitesDiscrepancyBySiteForPartner($partner, $this->getParams())
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

        return $this->getResult(
            $publisher instanceof SubPublisherInterface
                ? $this->getReportBuilder()->getAllSitesDiscrepancyByAdTagForPartnerWithSubPublisher($partner, $publisher, $this->getParams())
                : $this->getReportBuilder()->getAllSitesDiscrepancyByAdTagForPartner($partner, $this->getParams())
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

        return $this->getResult(
            $this->getReportBuilder()->getSiteDiscrepancyByDayForPartner($partner, $site, $this->getParams())
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

        return $this->getResult(
            $publisher instanceof SubPublisherInterface
                ? $this->getReportBuilder()->getSiteDiscrepancyByAdTagForPartnerWithSubPublisher($adNetwork, $site, $publisher, $this->getParams())
                : $this->getReportBuilder()->getSiteDiscrepancyByAdTagForPartner($adNetwork, $site, $this->getParams())
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
     * @param $result
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function getResult($result)
    {
        if ($result === false
            || (is_array($result) && count($result) < 1)
        ) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        return $result;
    }

    private function createFile($reportData, $filePath)
    {
//        $handle = fopen($filePath, 'w+');
//
//        if (!$handle) {
//            return false;
//        }
//
//        $interator = new Trave
//
//        while (false !== ($row = $interator->next())) {
//            // add a line in the csv file. You need to implement a toArray() method
//            // to transform your object into an array
//            fputcsv($handle, $row[0]->toArray());
//            // used to limit the memory consumption
//            $em->detach($row[0]);
//        }
//
//        fclose($handle);
    }
    /**
     * @return \Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilderInterface
     */
    private function getReportBuilder()
    {
        return $this->get('tagcade.service.report.unified_report.selector.report_builder');
    }
}
