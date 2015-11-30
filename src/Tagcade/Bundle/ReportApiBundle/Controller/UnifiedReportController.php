<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AccountManagement as AccountManagementReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily as DailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\DomainImpression as DomainImpressionReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\CountryDaily as CountryDailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
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
    static $REPORT_TYPE_MAP = [
        'adtag' => ['Pulse Point'],
        'daily' => ['Pulse Point'],
        'site' => ['Pulse Point'],
        'country' => ['Pulse Point']
    ];

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="breakDown")
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @param $id
     * @param Request $request
     * @throws NotSupportedException
     * @return array
     */
    public function getPartnerReportsAction($id, Request $request)
    {
        /* validate adNetworkPartner by $id */
        $adNetworkPartner = $this->get('tagcade.repository.ad_network_partner')->find($id);

        if(!$adNetworkPartner instanceof AdNetworkPartner) {
            throw new NotFoundHttpException('Not found that AdNetwork Partner');
        }

        /* validate breakDown */
        $breakDown = $request->query->get('breakDown', null);
        if (!$this->isSupportedReportType($adNetworkPartner, $breakDown)) {
            throw new NotSupportedException('Not support that breakDown as ' . $breakDown);
        }

        /* validate publisherId */
        $user = $this->getUser();

        $publisher = $user;

        if ($this->getUser() instanceof AdminInterface) {
            $publisherId = $request->query->get('publisher', null);
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                throw new NotFoundHttpException('Not found that publisher');
            }
        }

        return $this->getReports($publisher, $breakDown);
    }

    /**
     * check if supported breakDown
     * @param AdNetworkPartner $adNetworkPartner
     * @param $breakDown
     * @return bool|ReportTypeInterface
     */
    private function isSupportedReportType(AdNetworkPartner $adNetworkPartner, $breakDown)
    {
        if(!array_key_exists($breakDown, self::$REPORT_TYPE_MAP)
            || !in_array($adNetworkPartner->getName(), self::$REPORT_TYPE_MAP[$breakDown])
        ) {
            return false;
        }

        return true;
    }

    /**
     * get Reports by publisher and breakDown
     * @param $publisher
     * @param $breakDown
     * @throws NotSupportedException
     */
    private function getReports($publisher, $breakDown)
    {
        if ('adtag' === $breakDown) {
            return $this->getAccountManagementReportAction($publisher);
        } elseif ('daily' === $breakDown) {
            return $this->getDailyReportAction($publisher);
        } elseif ('site' === $breakDown) {
            return $this->getDomainImpressionReportAction($publisher);
        } elseif ('country' === $breakDown) {
            return $this->getCountryDailyReportAction($publisher);
        }

        throw new NotSupportedException('Not support that breakDown as ' . $breakDown);
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getAccountManagementReportAction(PublisherInterface $publisher)
    {
        $service = $this->get('tagcade.service.report.unified_report.selector.report_selector');

        return $this->getResult($service->getReports(new AccountManagementReportType($publisher, $tagId = null), $this->getParams()));
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getDailyReportAction(PublisherInterface $publisher)
    {
        $service = $this->get('tagcade.service.report.unified_report.selector.report_selector');

        return $this->getResult($service->getReports(new DailyReportType($publisher, $date = new \DateTime()), $this->getParams()));
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getDomainImpressionReportAction(PublisherInterface $publisher)
    {
        $service = $this->get('tagcade.service.report.unified_report.selector.report_selector');

        return $this->getResult($service->getReports(new DomainImpressionReportType($publisher, $date = new \DateTime()), $this->getParams()));
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getCountryDailyReportAction(PublisherInterface $publisher)
    {
        $service = $this->get('tagcade.service.report.unified_report.selector.report_selector');

        return $this->getResult($service->getReports(new CountryDailyReportType($publisher, $country = null, $tagId = null), $this->getParams()));
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
