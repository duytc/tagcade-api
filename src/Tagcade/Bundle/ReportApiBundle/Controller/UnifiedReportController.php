<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AccountManagement as AccountManagementReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily as DailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\DomainImpression as DomainImpressionReportType;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

/**
 * @Security("has_role('ROLE_ADMIN') or ( has_role('ROLE_PUBLISHER') and has_role('MODULE_UNIFIED_REPORT') )")
 *
 * Only allow admins and publishers with the display module enabled
 */
class UnifiedReportController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/accountManagement")
     *
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @param Request $request
     * @return array
     */
    public function getAccountManagementReportAction(Request $request)
    {
        $service = $this->get('tagcade.service.report.unified_report.selector.report_selector');

        $user = $this->getUser();

        $publisher = $user;

        if ($this->getUser() instanceof AdminInterface) {
            $publisherId = $request->query->get('publisher', null);
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                throw new NotFoundHttpException('Not found that publisher');
            }
        }

        return $this->getResult($service->getReports(new AccountManagementReportType($publisher, $date = new \DateTime()), $this->getParams()));
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/daily")
     *
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @param Request $request
     * @return array
     */
    public function getDailyReportAction(Request $request)
    {
        $service = $this->get('tagcade.service.report.unified_report.selector.report_selector');

        $user = $this->getUser();

        $publisher = $user;

        if ($this->getUser() instanceof AdminInterface) {
            $publisherId = $request->query->get('publisher', null);
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                throw new NotFoundHttpException('Not found that publisher');
            }
        }

        return $this->getResult($service->getReports(new DailyReportType($publisher, $date = new \DateTime()), $this->getParams()));
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/domainImpression")
     *
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @param Request $request
     * @return array
     */
    public function getDomainImpressionReportAction(Request $request)
    {
        $service = $this->get('tagcade.service.report.unified_report.selector.report_selector');

        $user = $this->getUser();

        $publisher = $user;

        if ($this->getUser() instanceof AdminInterface) {
            $publisherId = $request->query->get('publisher', null);
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                throw new NotFoundHttpException('Not found that publisher');
            }
        }

        return $this->getResult($service->getReports(new DomainImpressionReportType($publisher, $date = new \DateTime()), $this->getParams()));
    }

    /**
     * @return UnifiedReportParams
     */
    private function getParams()
    {
        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        return $this->_createParams($params);
    }

    /**
     * @var array $params
     * @return UnifiedReportParams
     */
    private function _createParams(array $params)
    {
        // create a params array with all values set to null
        $defaultParams = array_fill_keys([
            UnifiedReportParams::PARAM_START_DATE,
            UnifiedReportParams::PARAM_END_DATE,
            UnifiedReportParams::PARAM_GROUP
        ], null);

        $params = array_merge($defaultParams, $params);

        $dateUtil = $this->get('tagcade.service.date_util');
        $startDate = $dateUtil->getDateTime($params[UnifiedReportParams::PARAM_START_DATE], true);
        $endDate = $dateUtil->getDateTime($params[UnifiedReportParams::PARAM_END_DATE]);
        $group = $params[UnifiedReportParams::PARAM_GROUP];

        return new UnifiedReportParams($startDate, $endDate, $group);
    }

    /**
     * get Result
     * @param $result
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function getResult($result)
    {
        if ($result === false
            || (is_array($result) && count($result) < 1)
        ) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        return $result;
    }
}
