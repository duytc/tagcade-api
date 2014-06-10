<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Rest\RouteResource("SourceReport")
 */
class SourceReportController extends FOSRestController
{
    /**
     * @Rest\Get("/sourcereports/{domain}", requirements={"domain" = "[a-zA-Z._-]+"})
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
     * @Rest\QueryParam(name="from", requirements="\d{6}", nullable=true, description="Date of the report in format YYMMDD, defaults to the current day")
     * @Rest\QueryParam(name="to", requirements="\d{6}", nullable=true, description="If you want a report range, set this to a date in format YYMMDD - must be older than 'from'")
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", default=200, description="Limit the amount of rows returned in the report, -1 for no limit")
     * @Rest\QueryParam(name="sortField", requirements="[a-zA-Z_]+", default="visits", description="Column to sort by, i.e visits - not all columns are supported")
     *
     * @param string $domain domain name of the site you want reports for
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return array
     */
    public function cgetAction($domain, ParamFetcherInterface $paramFetcher)
    {
        $dateFrom = $paramFetcher->get('from', true);
        $dateTo = $paramFetcher->get('to', true);
        $rowOffset = $paramFetcher->get('rowOffset', true);
        $rowLimit = $paramFetcher->get('rowLimit', true);
        $sortField = $paramFetcher->get('sortField');

        $reports = $this->get('tagcade.handler.source_report')->getReports($domain, $dateFrom, $dateTo, $rowOffset, $rowLimit, $sortField);

        if (!$reports) {
            throw new NotFoundHttpException(sprintf('No Reports found for that query'));
        }

        return $reports;
    }

    /**
     * @Rest\Get("/sourcereports/{id}", requirements={"id" = "\d+"})
     *
     * Get source report by ID
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", default=200, description="Limit the amount of rows returned in the report, -1 for no limit")
     * @Rest\QueryParam(name="sortField", requirements="[a-zA-Z_]+", default="visits", description="Column to sort by, i.e visits - not all columns are supported")
     *
     *
     * @param int $id The source report ID
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return  array
     */
    public function getAction($id, ParamFetcherInterface $paramFetcher)
    {
        $rowOffset = $paramFetcher->get('rowOffset', true);
        $rowLimit = $paramFetcher->get('rowLimit', true);
        $sortField = $paramFetcher->get('sortField');

        $report = $this->get('tagcade.handler.source_report')->getReport($id, $rowOffset, $rowLimit, $sortField);

        if (!$report) {
            throw new NotFoundHttpException(sprintf('No Report found for that query'));
        }

        return $report;
    }
}
