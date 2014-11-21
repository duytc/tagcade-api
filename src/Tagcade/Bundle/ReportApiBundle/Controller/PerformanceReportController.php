<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Model\ModelInterface;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;

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
        $publishers = $this->get('tagcade_user.domain_manager.user')->allPublisherRoles();

        return $this->getReport(new PlatformReportTypes\Platform($publishers));
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
        $publishers = $this->get('tagcade_user.domain_manager.user')->allPublisherRoles();

        $reportTypes = array_map(function(PublisherInterface $publisher) {
            return new PlatformReportTypes\Account($publisher);
        }, $publishers);

        return $this->getReport($reportTypes);
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

        return $this->getReport(new PlatformReportTypes\Account($publisher));
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

        return $this->getReport(new PlatformReportTypes\Account($publisher));
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

        return $this->getAllAdNetworkReports($publisher);
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

        return $this->getAllAdNetworkReports($publisher);
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

        return $this->getReport(new AdNetworkReportTypes\AdNetwork($adNetwork));
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

        $sites = $this->get('tagcade.domain_manager.site')->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);

        $reportTypes = array_map(function($site) use($adNetwork) {
            return new AdNetworkReportTypes\Site($site, $adNetwork);
        }, $sites);

        return $this->getReport($reportTypes);
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

        return $this->getReport(new AdNetworkReportTypes\Site($site, $adNetwork));
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

        return $this->getAllSiteReports($publisher);
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

        return $this->getAllSiteReports($publisher);
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

        return $this->getReport(new PlatformReportTypes\Site($site));
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

        $adSlots = $site->getAdSlots()->toArray();

        $reportTypes = array_map(function($adSlot) {
                return new PlatformReportTypes\AdSlot($adSlot);
            }, $adSlots);

        return $this->getReport($reportTypes);
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

        return $this->getReport(new PlatformReportTypes\AdSlot($adSlot));
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

        $adTags = $adSlot->getAdTags()->toArray();

        $reportTypes = array_map(function($adTag) {
                return new PlatformReportTypes\AdTag($adTag);
            }, $adTags);

        return $this->getReport($reportTypes);
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

        return $this->getReport(new PlatformReportTypes\AdTag($adTag));
    }

    protected function getOptionalParam($param, $default = null)
    {
        try {
            // fosrestbundle throws exception if parameter is not defined
            $value = $this->get('fos_rest.request.param_fetcher')->get((string) $param, true);
        } catch (\InvalidArgumentException $e) {
            $value = $default;
        }

        return $value;
    }

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @return array
     */
    protected function getReport($reportType)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startDate = $paramFetcher->get('startDate', true);
        $endDate = $paramFetcher->get('endDate', true);
        $group = $this->getOptionalParam('group', $default = false);
        $expand = $this->getOptionalParam('expand', $default = false);

        $group = filter_var($group, FILTER_VALIDATE_BOOLEAN);
        $expand = filter_var($expand, FILTER_VALIDATE_BOOLEAN);

        $selector = $this->get('tagcade.service.report.performance_report.display.selector.report_selector');

        if (is_array($reportType)) {
            return $selector->getMultipleReports($reportType, $startDate, $endDate, $group, $expand);
        }
        // else
        return $selector->getReports($reportType, $startDate, $endDate, $group, $expand);
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

    protected function getAllAdNetworkReports(PublisherInterface $publisher)
    {
        $adNetworks = $this->get('tagcade.domain_manager.ad_network')->getAdNetworksForPublisher($publisher);

        $reportTypes = array_map(function($adNetwork) {
                return new AdNetworkReportTypes\AdNetwork($adNetwork);
            }, $adNetworks);

        return $this->getReport($reportTypes);
    }

    protected function getAllSiteReports(PublisherInterface $publisher)
    {
        $sites = $this->get('tagcade.domain_manager.site')->getSitesForPublisher($publisher);

        $reportTypes = array_map(function($site) {
                return new PlatformReportTypes\Site($site);
            }, $sites);

        return $this->getReport($reportTypes);
    }
}
