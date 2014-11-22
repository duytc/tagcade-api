<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\PublisherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Model\ModelInterface;
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
     * @Rest\Get("/performancereports/platform")
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
     * @Rest\Get("/performancereports/publishers", requirements={"publisherId" = "\d+"})
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
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/performancereports/publishers/{publisherId}", requirements={"publisherId" = "\d+"})
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
     * @Security("has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/performancereports/publishers/current")
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @return array
     */
    public function getPublisherReportForCurrentPublisherAction()
    {
        $publisher = $this->getCurrentPublisher();

        return $this->getReportBuilder()->getPublisherReport($publisher, $this->getParams());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/performancereports/publishers/{publisherId}/adnetworks", requirements={"publisherId" = "\d+"})
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
     * @Security("has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/performancereports/publishers/current/adnetworks")
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     */
    public function getCurrentPublisherAdNetworksAction()
    {
        $publisher = $this->getCurrentPublisher();

        return $this->getReportBuilder()->getPublisherAdNetworksReport($publisher, $this->getParams());
    }

    /**
     * @Rest\Get("/performancereports/adnetworks/{adNetworkId}", requirements={"adNetworkId" = "\d+"})
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
     * @Rest\Get("/performancereports/adnetworks/{adNetworkId}/sites", requirements={"adNetworkId" = "\d+"})
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
     * @Rest\Get("/performancereports/adnetworks/{adNetworkId}/sites/{siteId}", requirements={"adNetworkId" = "\d+", "siteId" = "\d+"})
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
     * @Rest\Get("/performancereports/sites")
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
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/performancereports/publishers/{publisherId}/sites", requirements={"publisherId" = "\d+"})
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
     * @Security("has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/performancereports/publishers/current/sites")
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @return array
     */
    public function getCurrentPublisherSitesAction()
    {
        $publisher = $this->getCurrentPublisher();

        return $this->getReportBuilder()->getPublisherSitesReport($publisher, $this->getParams());
    }

    /**
     * @Rest\Get("/performancereports/sites/{siteId}", requirements={"siteId" = "\d+"})
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
     * @Rest\Get("/performancereports/sites/{siteId}/adslots", requirements={"siteId" = "\d+"})
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
     * @Rest\Get("/performancereports/adslots/{adSlotId}", requirements={"adSlotId" = "\d+"})
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
     * @Rest\Get("/performancereports/adslots/{adSlotId}/adtags", requirements={"adSlotId" = "\d+"})
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
     * @Rest\Get("/performancereports/adtags/{adTagId}", requirements={"adTagId" = "\d+"})
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
     * @param ModelInterface $entity The entity instance
     * @return bool
     * @throws AccessDeniedException
     */
    protected function checkUserPermission(ModelInterface $entity)
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

        return $publisher;
    }

    /**
     * @return PublisherInterface
     */
    protected function getCurrentPublisher()
    {
        $publisher = $this->get('tagcade_user.domain_manager.user')->getUserRole($this->getUser());

        if (!$publisher instanceof PublisherInterface) {
            throw new LogicException('The user should have the publisher role');
        }

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
     * @return array
     */
    protected function getParams()
    {
        return $this->get('fos_rest.request.param_fetcher')->all($strict = true);
    }
}
