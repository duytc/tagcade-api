<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Bundle\AdminApiBundle\Repository\ActionLogRepositoryInterface;
use Tagcade\Exception\Report\InvalidDateException;

/**
 * @Rest\RouteResource("Logs")
 */
class ActionLogController extends FOSRestController
{
    /**
     *
     * @Rest\Get("/logs")
     *
     * Get performance reports for the platform with optional date range.
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the log in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want a log range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", nullable=true, description="Order number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", default=200, description="Limit the amount of rows returned in the report, -1 for no limit")
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return array
     *
     * @throws InvalidDateException if start date is greater than end date
     */
    public function getAction(ParamFetcherInterface $paramFetcher)
    {
        // TODO - move DateUtil service to outside of report namespace
        $dateUtil = $this->get('tagcade.service.report.date_util');

        $startDate = $dateUtil->getDateTime( $paramFetcher->get('startDate', true));
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate', true));
        $rowOffset = intval($paramFetcher->get('rowOffset', true));
        $rowLimit = intval($paramFetcher->get('rowLimit', true));

        if (!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate > $endDate) {
            throw new InvalidDateException('start date must be before the end date');
        }

        /**
         * @var ActionLogRepositoryInterface
         */
        $actionLogRepository = $this->getDoctrine()->getRepository('TagcadeAdminApiBundle:ActionLog');

        return $actionLogRepository->getLogsForDateRange($startDate, $endDate, $rowOffset, $rowLimit);

    }

} 