<?php

namespace Tagcade\Bundle\StatisticsApiBundle\Controller;

use DateTime;
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
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Get projected bill for the platform with optional date range.
     *
     * @return array
     */
    public function getPlatformProjectedbillAction()
    {
        return $this->get('tagcade.service.statistics')
            ->getProjectedBilledAmountForAllPublishers();
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
     *
     * Get billed amount for a publisher with month range (month format YYYY-MM).
     *
     * @Rest\Get("/accounts/{publisherId}/billedAmount", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startMonth", requirements="\d{4}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endMonth", requirements="\d{4}-\d{2}")
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getAccountBillAmountAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('startMonth'));
        $endMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('endMonth'));

        return $this->get('tagcade.service.statistics')->getAccountBilledAmountByMonth($publisher, $startMonth, $endMonth);
    }

    /**
     *
     * Get est. revenue for a publisher with month range (month format YYYY-MM).
     *
     * @Rest\Get("/accounts/{publisherId}/revenue", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startMonth", requirements="\d{4}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endMonth", requirements="\d{4}-\d{2}")
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getAccountRevenueAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('startMonth'));
        $endMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('endMonth'));

        return $this->get('tagcade.service.statistics')->getAccountRevenueByMonth($publisher, $startMonth, $endMonth);
    }

    /**
     *
     * Get summary stats for a publisher with month range (month format YYYY-MM).
     *
     * @Rest\Get("/accounts/{publisherId}/summary", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startMonth", requirements="\d{4}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endMonth", requirements="\d{4}-\d{2}")
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getAccountSummaryAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('startMonth'));
        $endMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('endMonth'));

        return $this->get('tagcade.service.statistics')->getAccountSummaryByMonth($publisher, $startMonth, $endMonth);
    }

    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Get summary stats for a platform with month range (month format YYYY-MM).
     *
     * @Rest\Get("/platform/summary")
     *
     * @Rest\QueryParam(name="startMonth", requirements="\d{4}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endMonth", requirements="\d{4}-\d{2}")
     *
     *
     * @return array
     */
    public function getPlatformSummaryAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('startMonth'));
        $endMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('endMonth'));

        return $this->get('tagcade.service.statistics')->getPlatformSummaryByMonth($startMonth, $endMonth);
    }

    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Get platform billed amount with month range (month format YYYY-MM).
     *
     * @Rest\Get("/platform/billedAmount")
     *
     * @Rest\QueryParam(name="startMonth", requirements="\d{4}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endMonth", requirements="\d{4}-\d{2}")
     *
     *
     * @return array
     */
    public function getPlatformBillAmountAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('startMonth'));
        $endMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('endMonth'));

        return $this->get('tagcade.service.statistics')->getPlatformBilledAmountByMonth($startMonth, $endMonth);
    }

    /**
     * @param int $publisherId
     * @return \Tagcade\Model\User\Role\PublisherInterface
     */
    protected function getPublisher($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        if ( false === $this->get('security.context')->isGranted('view', $publisher) ) {
            throw new AccessDeniedException('You do not have permission to view this');
        }

        return $publisher;
    }
}
