<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use DateTime;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Bundle\AdminApiBundle\Repository\ActionLogRepositoryInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\Report\InvalidDateException;
use Tagcade\Domain\DTO\ActionLog;
use Tagcade\Model\User\Role\PublisherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @Rest\QueryParam(name="loginLogs", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="publisherId", requirements="\d+", nullable=true)
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

        $startDate = $paramFetcher->get('startDate', true);
        $endDate = $paramFetcher->get('endDate', true);
        $rowLimit = $paramFetcher->get('rowLimit', true);
        $rowOffset = $paramFetcher->get('rowOffset', true);
        $loginLogs = filter_var($paramFetcher->get('loginLogs'), FILTER_VALIDATE_BOOLEAN);
        $publisherId = $paramFetcher->get('publisherId', true);

        if(!$startDate)
        {
            $startDate = new DateTime('6 days ago');
        }

        if(!$endDate)
        {
            $endDate = new DateTime('today');
        }

        $startDate = $dateUtil->getDateTime($startDate, true);
        $endDate = $dateUtil->getDateTime($endDate, true);

        $rowOffset = intval($rowOffset);
        $rowLimit = intval($rowLimit);

        if (!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate > $endDate) {
            throw new InvalidDateException('start date must be before the end date');
        }

        $publisher = $publisherId ? $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId) : null;

        /**
         * @var ActionLogRepositoryInterface
         */
        $actionLogRepository = $this->getDoctrine()->getRepository('TagcadeAdminApiBundle:ActionLog');

        $numRows = $actionLogRepository->getTotalRows($startDate, $endDate, $publisher, $loginLogs);
        $logsList = $actionLogRepository->getLogsForDateRange($startDate, $endDate, $rowOffset, $rowLimit, $publisher, $loginLogs);

        return new ActionLog($numRows, $logsList);

    }
}