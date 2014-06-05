<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcherInterface;

/**
 * @Rest\RouteResource("SourceReport")
 */
class SourceReportController extends FOSRestController implements ClassResourceInterface
{
    /**
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
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", default="250", description="Limit the amount of rows returned in the report, -1 for no limit")
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
        $rowLimit = $paramFetcher->get('rowLimit');

        return $this->get('tagcade.handler.source_report')->getReports($domain, $dateFrom, $dateTo, $rowLimit);
    }
}
