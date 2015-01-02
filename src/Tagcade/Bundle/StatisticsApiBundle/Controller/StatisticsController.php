<?php

namespace Tagcade\Bundle\StatisticsApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;

class StatisticsController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Get statistics for the platform with optional date range.
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @return array
     */
    public function getPlatformAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'));
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'));

        return $this->get('tagcade.service.statistics')
            ->getAdminDashboard($startDate, $endDate);
    }

    /**
     *
     * Get statistics for a publisher with optional date range.
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getAccountAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'));
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'));

        return $this->get('tagcade.service.statistics')
            ->getPublisherDashboard($publisher, $startDate, $endDate);
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/projectedbill", requirements={"publisherId" = "\d+"})
     *
     * @param $publisherId
     * @return string
     */
    public function getAccountProjectedBillAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        return $this->get('tagcade.service.statistics')->getProjectedBilledAmountForPublisher($publisher);
    }

    /**
     * @param int $publisherId
     * @return \Tagcade\Model\User\Role\Publisher
     */
    protected function getPublisher($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.user')->findPublisher($publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        if ( false === $this->get('security.context')->isGranted('view', $publisher) ) {
            throw new AccessDeniedException('You do not have permission to view this');
        }

        return $publisher;
    }
}
