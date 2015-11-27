<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily as DailyReportType;
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
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/accountManagement")
     *
     * @Rest\QueryParam(name="publisherId", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @return array
     */
    public function getAccountManagementReportAction()
    {
        return $this->view(null, Codes::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/daily")
     *
     * @Rest\QueryParam(name="publisherId", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @return array
     */
    public function getDailyReportAction()
    {
        $service = $this->get('tagcade.service.report.unified_report.pulse_point.selector.report_selector');

        $user = $this->getUser();

        if (!$user instanceof AdminInterface
            && !$user instanceof PublisherInterface
        ) {
            return $this->view(null, Codes::HTTP_FORBIDDEN);
        }

        if ($this->getUser() instanceof AdminInterface) {
            // get for multiple...
            //return [];

            /** @var PublisherInterface $publisher */
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find(12);
            return $service->getReports(new DailyReportType($publisher, $date = new \DateTime()), $this->getParams());
        }

        return $service->getReports(new DailyReportType($publisher = $user, $date = new \DateTime()), $this->getParams());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/domainImpression")
     *
     * @Rest\QueryParam(name="publisherId", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @return array
     */
    public function getDomainImpressionReportAction()
    {
        return $this->view(null, Codes::HTTP_NOT_IMPLEMENTED);
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
            UnifiedReportParams::PARAM_END_DATE
        ], null);

        $params = array_merge($defaultParams, $params);

        $dateUtil = $this->get('tagcade.service.date_util');
        $startDate = $dateUtil->getDateTime($params[UnifiedReportParams::PARAM_START_DATE], true);
        $endDate = $dateUtil->getDateTime($params[UnifiedReportParams::PARAM_END_DATE]);

        return new UnifiedReportParams($startDate, $endDate);
    }
}
