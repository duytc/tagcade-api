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

/**
 * @Security("has_role('ROLE_ADMIN') or ( has_role('ROLE_PUBLISHER') )")
 *
 * Only allow admins and publishers with the display module enabled
 *
 * @Rest\RouteResource("Statistics")
 */
class StatisticsController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/statistics/platform/reports")
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
     * @Rest\QueryParam(name="deepLength", requirements="\d+", nullable=true, description="Show top Site, if null would getting value default")
     *
     * @return array
     */
    public function getPlatformAction()
    {
        $publishers = $this->get('tagcade_user.domain_manager.user')->allPublisherRoles();

        return $this->getSelector(new PlatformReportTypes\Platform($publishers));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/statistics/account/{publisherId}/reports", requirements={"publisherId" = "\d+"})
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
     * @Rest\QueryParam(name="deepLength", requirements="\d+", nullable=true, description="Show top Site, if null would getting value default")
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

        return $this->getSelector(new PlatformReportTypes\Account($publisher));
    }

    /**
     * @Security("has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/statistics/account/reports")
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
     * @Rest\QueryParam(name="deepLength", requirements="\d+", nullable=true, description="Show top Site, if null would getting value default")
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

        return $this->getSelector(new PlatformReportTypes\Account($publisher));
    }

    /**
     * @Rest\Get("/statistics/site/{siteId}/reports", requirements={"siteId" = "\d+"})
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
     * @Rest\QueryParam(name="deepLength", requirements="\d+", nullable=true, description="Show top Site, if null would getting value default")
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

        return $this->getSelector(new PlatformReportTypes\Site($site));
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return array|Object
     */
    public function getSelector(ReportTypeInterface $reportType)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $dateUtil = $this->get('tagcade.service.date_util');

        $startDate = $dateUtil->getDateTime( $paramFetcher->get('startDate', true));
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate', true));
        $deepLength = (int) $paramFetcher->get('deepLength');

        return $this->get('tagcade.service.statistics.selector')
            ->getStatistics($reportType, $startDate, $endDate, $deepLength);
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
