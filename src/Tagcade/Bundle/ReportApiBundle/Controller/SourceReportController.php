<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * @Rest\RouteResource("SourceReport")
 * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_PUBLISHER') and has_role('MODULE_ANALYTICS'))")
 */
class SourceReportController extends FOSRestController
{
    /**
     * @Rest\Get("/{siteId}", requirements={"siteId" = "\d+"})
     *
     * Get source reports for a site with optional date range.
     *
     * @ApiDoc(
     *  section = "Source Reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Order number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", nullable=true, description="Limit the amount of rows returned in the report")
     *
     * @param int $siteId ID of the site you want the report for
     *
     * @return array
     */
    public function getSiteReportAction($siteId)
    {
        $dateUtil = $this->get('tagcade.service.date_util');
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        /** @var SiteInterface $site */
        $site = $this->container->get('tagcade.domain_manager.site')->find($siteId);

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate', true), $returnTodayIfEmpty = true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate', true));

        if (!$site) {
            throw new NotFoundHttpException('This site does not exist or you do not have access');
        }

        $publisher = $site->getPublisher();

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException();
        }

        if (!$publisher->hasAnalyticsModule()) {
            throw new NotFoundHttpException();
        }

        if (false === $this->get('security.context')->isGranted('view', $site)) {
            throw new AccessDeniedException('You do not have permission to view this site');
        }

        $reports = $this->get('tagcade.service.report.source_report.selector.report_selector')->getReports($site, $startDate, $endDate);

        if (!$reports) {
            throw new NotFoundHttpException('No Reports found for that query');
        }

        return $reports;
    }

    /**
     * @Rest\Get("/accounts/{publisherId}", requirements={"publisherId" = "\d+"})
     *
     * Get source reports for a site with optional date range.
     *
     * @ApiDoc(
     *  section = "Source Reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Order number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", nullable=true, description="Limit the amount of rows returned in the report")
     *
     * @param int $publisherId ID of the publisher you want the report for
     *
     * @return array
     */
    public function getPublisherByDayReportAction($publisherId)
    {
        $dateUtil = $this->get('tagcade.service.date_util');
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        /** @var PublisherInterface $publisher */
        $publisher = $this->container->get('tagcade_user.domain_manager.publisher')->find($publisherId);

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate', true), $returnTodayIfEmpty = true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate', true));
        if (!$endDate) {
            $endDate = $startDate;
        }

        if (!$publisher) {
            throw new NotFoundHttpException('This publisher does not exist or you do not have access');
        }

        $reports = $this->get('tagcade.service.report.source_report.selector.report_selector')->getPublisherByDayReport($publisher, $startDate, $endDate);

        if (!$reports) {
            throw new NotFoundHttpException('No Reports found for that query');
        }

        return $reports;
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/sites", requirements={"publisherId" = "\d+"})
     *
     * Get source reports for a site with optional date range.
     *
     * @ApiDoc(
     *  section = "Source Reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Order number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", nullable=true, description="Limit the amount of rows returned in the report")
     *
     * @param int $publisherId ID of the publisher you want the report for
     *
     * @return array
     */
    public function getPublisherBySiteReportAction($publisherId)
    {
        $dateUtil = $this->get('tagcade.service.date_util');
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        /** @var PublisherInterface $publisher */
        $publisher = $this->container->get('tagcade_user.domain_manager.publisher')->find($publisherId);

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate', true), $returnTodayIfEmpty = true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate', true));

        if (!$publisher) {
            throw new NotFoundHttpException('This publisher does not exist or you do not have access');
        }

        $reports = $this->get('tagcade.service.report.source_report.selector.report_selector')->getPublisherBySiteReport($publisher, $startDate, $endDate);

        if (!$reports) {
            throw new NotFoundHttpException('No Reports found for that query');
        }

        return $reports;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Get("/platform")
     *
     * Get source reports for a site with optional date range.
     *
     * @ApiDoc(
     *  section = "Source Reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Order number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", nullable=true, description="Limit the amount of rows returned in the report")
     *
     * @return array
     */
    public function getPlatformByDayReportAction()
    {
        $dateUtil = $this->get('tagcade.service.date_util');
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate', true), $returnTodayIfEmpty = true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate', true));

        $reports = $this->get('tagcade.service.report.source_report.selector.report_selector')->getPlatformByDayReport($startDate, $endDate);

        if (!$reports) {
            throw new NotFoundHttpException('No Reports found for that query');
        }

        return $reports;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Get("/platform/accounts")
     *
     * Get source reports for a site with optional date range.
     *
     * @ApiDoc(
     *  section = "Source Reports",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the report in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a report range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Order number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", nullable=true, description="Limit the amount of rows returned in the report")
     *
     * @return array
     */
    public function getPlatformByPublisherReportAction()
    {
        $dateUtil = $this->get('tagcade.service.date_util');
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate', true), $returnTodayIfEmpty = true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate', true));

        $reports = $this->get('tagcade.service.report.source_report.selector.report_selector')->getPlatformByPublisherReport($startDate, $endDate);

        if (!$reports) {
            throw new NotFoundHttpException('No Reports found for that query');
        }

        return $reports;
    }
}
