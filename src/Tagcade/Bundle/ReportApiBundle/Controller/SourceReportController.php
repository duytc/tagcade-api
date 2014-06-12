<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Rest\RouteResource("SourceReport")
 */
class SourceReportController extends FOSRestController
{
    /**
     * @Rest\Get("/sourcereports/{siteId}", requirements={"siteId" = "\d+"})
     *
     * Get source reports for a site with optional date range.
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="from", requirements="\d{6}", nullable=true, description="Date of the report in format YYMMDD, defaults to the yesterday")
     * @Rest\QueryParam(name="to", requirements="\d{6}", nullable=true, description="If you want a report range, set this to a date in format YYMMDD - must be older than 'from'")
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", default=200, description="Limit the amount of rows returned in the report, -1 for no limit")
     * @Rest\QueryParam(name="sortField", requirements="[a-zA-Z_]+", nullable=true, description="Column to sort by, i.e visits - not all columns are supported")
     * @Rest\QueryParam(name="viewPreset", requirements="[a-zA-Z_]+", nullable=true, description="A view preset is a defined subset of columns, i.e display_only, video_only")
     *
     * @param int $siteId ID of the site you want the report for
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return array
     */
    public function cgetAction($siteId, ParamFetcherInterface $paramFetcher)
    {
        $site = $this->container->get('tagcade_api.handler.site')->get($siteId);

        if (!$site) {
            throw new NotFoundHttpException('This site does not exist or you do not have access');
        }

        if (false === $this->get('security.context')->isGranted('view', $site)) {
            throw new AccessDeniedException('You do not have permission to view this site');
        }

        $dateFrom = $paramFetcher->get('from', true);
        $dateTo = $paramFetcher->get('to', true);
        $rowOffset = $paramFetcher->get('rowOffset', true);
        $rowLimit = $paramFetcher->get('rowLimit', true);
        $sortField = $paramFetcher->get('sortField');

        $reports = $this->get('tagcade.handler.source_report')->getReports($siteId, $dateFrom, $dateTo, $rowOffset, $rowLimit, $sortField);

        if (!$reports) {
            throw new NotFoundHttpException('No Reports found for that query');
        }

        return $reports;
    }
}
