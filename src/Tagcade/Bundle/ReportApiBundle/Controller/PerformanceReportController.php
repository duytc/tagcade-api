<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\PublisherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;

/**
 * @Security("has_role('ROLE_ADMIN') or ( has_role('ROLE_PUBLISHER') and has_role('MODULE_DISPLAY') )")
 *
 * Only allow admins and publishers with the display module enabled
 */
class PerformanceReportController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/platform")
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @return array
     */
    public function getPlatformAction()
    {
        return $this->getReportBuilder()->getPlatformReport($this->getParams());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/accounts", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @return array
     */
    public function getPublishersAction()
    {
        return $this->getReportBuilder()->getAllPublishersReport($this->getParams());
    }

    /**
     * @Rest\Get("/accounts/{publisherId}", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getPublisherAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        return $this->getReportBuilder()->getPublisherReport($publisher, $this->getParams());
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/adnetworks", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $publisherId
     * @return array
     */
    public function getPublisherAdNetworksAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        return $this->getReportBuilder()->getPublisherAdNetworksReport($publisher, $this->getParams());
    }

    /**
     * @Rest\Get("/adnetworks/{adNetworkId}", requirements={"adNetworkId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $adNetworkId
     * @return array
     */
    public function getAdnetworkAction($adNetworkId)
    {
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($adNetworkId);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That ad network does not exist');
        }

        $this->checkUserPermission($adNetwork);

        return $this->getReportBuilder()->getAdNetworkReport($adNetwork, $this->getParams());
    }

    /**
     * @Rest\Get("/adnetworks/{adNetworkId}/sites", requirements={"adNetworkId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $adNetworkId
     * @return array
     */
    public function getAdnetworkSitesAction($adNetworkId)
    {
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($adNetworkId);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That ad network does not exist');
        }

        $this->checkUserPermission($adNetwork);

        return $this->getReportBuilder()->getAdNetworkSitesReport($adNetwork, $this->getParams());
    }

    /**
     * @Rest\Get("/adnetworks/{adNetworkId}/sites/{siteId}", requirements={"adNetworkId" = "\d+", "siteId" = "\d+"})
     *
     * Get performance reports for an ad network and site with optional date range.
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $adNetworkId
     * @param int $siteId ID
     *
     * @return array
     */
    public function getAdNetworkSiteAction($adNetworkId, $siteId)
    {
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($adNetworkId);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That ad network does not exist');
        }

        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        $this->checkUserPermission($adNetwork);
        $this->checkUserPermission($site);

        return $this->getReportBuilder()->getAdNetworkSiteReport($adNetwork, $site, $this->getParams());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/sites")
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @return array
     */
    public function getSitesAction()
    {
        return $this->getReportBuilder()->getAllSitesReport($this->getParams());
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/sites", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $publisherId
     * @return array
     */
    public function getPublisherSitesAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        return $this->getReportBuilder()->getPublisherSitesReport($publisher, $this->getParams());
    }

    /**
     * @Rest\Get("/sites/{siteId}", requirements={"siteId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $siteId ID of the site you want the report for
     *
     * @return array
     */
    public function getSiteAction($siteId)
    {
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        $this->checkUserPermission($site);

        return $this->getReportBuilder()->getSiteReport($site, $this->getParams());
    }

    /**
     * @Rest\Get("/sites/{siteId}/adslots", requirements={"siteId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $siteId
     *
     * @return array
     */
    public function getSiteAdSlotsAction($siteId)
    {
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        $this->checkUserPermission($site);

        return $this->getReportBuilder()->getSiteAdSlotsReport($site, $this->getParams());
    }

    /**
     * @Rest\Get("/adslots/{adSlotId}", requirements={"adSlotId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @param int $adSlotId
     *
     * @return array
     */
    public function getAdSlotAction($adSlotId)
    {
        $adSlot = $this->get('tagcade.domain_manager.ad_slot')->find($adSlotId);

        if (!$adSlot) {
            throw new NotFoundHttpException('That ad slot does not exist');
        }

        $this->checkUserPermission($adSlot);

        return $this->getReportBuilder()->getAdSlotReport($adSlot, $this->getParams());
    }

    /**
     * @Rest\Get("/adslots/{adSlotId}/adtags", requirements={"adSlotId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @param int $adSlotId
     *
     * @return array
     */
    public function getAdSlotAdTagsAction($adSlotId)
    {
        $adSlot = $this->get('tagcade.domain_manager.ad_slot')->find($adSlotId);

        if (!$adSlot) {
            throw new NotFoundHttpException('That ad tag does not exist');
        }

        $this->checkUserPermission($adSlot);

        return $this->getReportBuilder()->getAdSlotAdTagsReport($adSlot, $this->getParams());
    }

    /**
     * @Rest\Get("/adtags/{adTagId}", requirements={"adTagId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @param int $adTagId
     *
     * @return array
     */
    public function getAdTagAction($adTagId)
    {
        $adTag = $this->get('tagcade.domain_manager.ad_tag')->find($adTagId);

        if (!$adTag) {
            throw new NotFoundHttpException('That ad tag does not exist');
        }

        $this->checkUserPermission($adTag);

        return $this->getReportBuilder()->getAdTagReport($adTag, $this->getParams());
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
     * @param $publisherId
     * @return PublisherInterface
     */
    protected function getPublisher($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.user')->findPublisher($publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        if (!$publisher instanceof PublisherInterface) {
            throw new LogicException('The user should have the publisher role');
        }

        $this->checkUserPermission($publisher);

        return $publisher;
    }

    /**
     * @return ReportBuilderInterface
     */
    protected function getReportBuilder()
    {
        return $this->get('tagcade.server.report.performance_report.display.selector.report_builder');
    }

    /**
     * @return Params
     */
    protected function getParams()
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
}
