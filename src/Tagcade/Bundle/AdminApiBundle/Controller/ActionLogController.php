<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Bundle\AdminApiBundle\Repository\ActionLogRepositoryInterface;
use Tagcade\Exception\Report\InvalidDateException;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tagcade\Domain\DTO\ActionLogs;

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
     * @Rest\QueryParam(name="rowOffset", requirements="\d+", default=0, description="Order number of rows to skip before rowLimit kicks in")
     * @Rest\QueryParam(name="rowLimit", requirements="\d+", default=10, description="Limit the amount of rows returned in the report, -1 for no limit")
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return array
     *
     * @throws InvalidDateException if start date is greater than end date
     */
    public function getAction(ParamFetcherInterface $paramFetcher)
    {
        $dateUtil = $this->get('tagcade.service.date_util');

        $paramStartDate = $paramFetcher->get('startDate', true);
        $paramEndDate = $paramFetcher->get('endDate', true);
        $paramRowLimit = $paramFetcher->get('rowLimit', true);
        $paramRowOffset = $paramFetcher->get('rowOffset', true);

        if(!$paramStartDate)
        {
            $paramStartDate = new DateTime('6 days ago');
        }

        if(!$paramEndDate)
        {
            $paramEndDate = new DateTime('today');
        }

        $startDate = $dateUtil->getDateTime($paramStartDate);
        $endDate = $dateUtil->getDateTime($paramEndDate);
        $rowOffset = intval($paramRowOffset);
        $rowLimit = intval($paramRowLimit);

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

        $totalRecords = $actionLogRepository->getTotalRecords($startDate, $endDate);
        $logsList = $actionLogRepository->getLogsForDateRange($startDate, $endDate, $rowOffset, $rowLimit);

        return new ActionLogs($totalRecords, $logsList);

    }

} 