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
 *
 * @Rest\RouteResource("PerformanceReports")
 */
class PerformanceReportController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/performancereports/platform/reports")
     *
     * Get performance reports for the platform with optional date range.
     *
     * @ApiDoc(
     *  section = "reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true, description="A report can be expanded to return sub reports instead of the calculated totals")
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
     * @Rest\Get("/performancereports/account/{publisherId}/reports", requirements={"publisherId" = "\d+"})
     *
     * Get performance reports for a publisher with optional date range.
     *
     * @ApiDoc(
     *  section = "reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true, description="A report can be expanded to return sub reports instead of the calculated totals")
     *
     * @param int $publisherId ID of the publisher you want the report for
     *
     * @return array
     */
    public function getAccountReportAction($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.user')->findPublisher($publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        return $this->getReport(new PlatformReportTypes\Account($publisher));
    }

    /**
     * @Security("has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/performancereports/account/reports")
     *
     * Get performance reports for the current publisher with optional date range. Must be logged in as a publisher to access
     *
     * @ApiDoc(
     *  section = "reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true, description="A report can be expanded to return sub reports instead of the calculated totals")
     *
     * @return array
     */
    public function getAccountReportForCurrentPublisherAction()
    {
        $publisher = $this->get('tagcade_user.domain_manager.user')->getUserRole($this->getUser());

        if (!$publisher instanceof PublisherInterface) {
            throw new LogicException('The user should have the publisher role');
        }

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        return $this->getReport(new PlatformReportTypes\Account($publisher));
    }

    /**
     * @Rest\Get("/performancereports/site/{siteId}/reports", requirements={"siteId" = "\d+"})
     *
     * Get performance reports for a site with optional date range.
     *
     * @ApiDoc(
     *  section = "reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true, description="A report can be expanded to return sub reports instead of the calculated totals")
     *
     * @param int $siteId ID of the site you want the report for
     *
     * @return array
     */
    public function getSiteReportAction($siteId)
    {
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        $this->checkUserPermission($site);

        return $this->getReport(new PlatformReportTypes\Site($site));
    }

    /**
     * @Rest\Get("/performancereports/adslot/{adSlotId}/reports", requirements={"adSlotId" = "\d+"})
     *
     * Get performance reports for an ad slot with optional date range.
     *
     * @ApiDoc(
     *  section = "reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true, description="A report can be expanded to return sub reports instead of the calculated totals")
     *
     * @param int $adSlotId ID of the ad slot you want the report for
     *
     * @return array
     */
    public function getAdSlotReportAction($adSlotId)
    {
        $adSlot = $this->get('tagcade.domain_manager.ad_slot')->find($adSlotId);

        if (!$adSlot) {
            throw new NotFoundHttpException('That ad slot does not exist');
        }

        $this->checkUserPermission($adSlot);

        return $this->getReport(new PlatformReportTypes\AdSlot($adSlot));
    }

    /**
     * @Rest\Get("/performancereports/adtag/{adTagId}/reports", requirements={"adTagId" = "\d+"})
     *
     * Get performance reports for an ad tag with optional date range.
     *
     * @ApiDoc(
     *  section = "reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param int $adTagId ID of the ad tag you want the report for
     *
     * @return array
     */
    public function getAdTagReportAction($adTagId)
    {
        $adTag = $this->get('tagcade.domain_manager.ad_tag')->find($adTagId);

        if (!$adTag) {
            throw new NotFoundHttpException('That ad tag does not exist');
        }

        $this->checkUserPermission($adTag);

        return $this->getReport(new PlatformReportTypes\AdTag($adTag));
    }

    /**
     * @Rest\Get("/performancereports/adnetwork/{adNetworkId}/reports", requirements={"adNetworkId" = "\d+"})
     *
     * Get performance reports for an ad network with optional date range.
     *
     * @ApiDoc(
     *  section = "reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true, description="A report can be expanded to return sub reports instead of the calculated totals")
     *
     * @param int $adNetworkId ID of the ad network you want the report for
     *
     * @return array
     */
    public function getAdNetworkReportAction($adNetworkId)
    {
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($adNetworkId);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That ad network does not exist');
        }

        $this->checkUserPermission($adNetwork);

        return $this->getReport(new AdNetworkReportTypes\AdNetwork($adNetwork));
    }

    /**
     * @Rest\Get("/performancereports/adnetwork/{adNetworkId}/site/{siteId}/reports", requirements={"adNetworkId" = "\d+", "siteId" = "\d+"})
     *
     * Get performance reports for an ad network and site with optional date range.
     *
     * @ApiDoc(
     *  section = "reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true, description="A report can be expanded to return sub reports instead of the calculated totals")
     *
     * @param int $adNetworkId ID of the ad network you want the report for
     * @param int $siteId ID of the site you want the report for
     *
     * @return array
     */
    public function getAdNetworkSiteReportAction($adNetworkId, $siteId)
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

    protected function getReport(ReportTypeInterface $reportType)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        try {
            // fosrestbundle throws exception if parameter is not defined
            $expand = $paramFetcher->get('expand', true);
        } catch (\InvalidArgumentException $e) {
            $expand = false;
        }

        $expand = filter_var($expand, FILTER_VALIDATE_BOOLEAN);

        return $this->get('tagcade.service.report.performance_report.display.creator.report_selector')
            ->getReports($reportType, $paramFetcher->get('startDate', true), $paramFetcher->get('endDate', true), $expand)
        ;
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
}
