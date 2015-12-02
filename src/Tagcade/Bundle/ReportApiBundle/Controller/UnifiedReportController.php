<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Core\PublisherPartner;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AccountManagement as AccountManagementReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagDomainImpression as AdTagDomainImpressionReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\CountryDaily as CountryDailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily as DailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\DomainImpression as DomainImpressionReportType;
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
    /* report types */
    const REPORT_TYPE_KEY_AD_TAG = 'adtag';
    const REPORT_TYPE_KEY_DAILY = 'daily';

    /* breakdown types */
    const BREAK_DOWN_KEY_DAY = 'day';
    const BREAK_DOWN_KEY_SITE = 'site';
    const BREAK_DOWN_KEY_COUNTRY = 'country';

    /* ad network partner name_canonicals */
    const AD_NETWORK_PARTNER_PULSE_POINT = 'pulse-point';

    /* drill down types */
    const PARAM_DRILL_BY_AD_TAG = 'drillByAdTag';
    const PARAM_DRILL_BY_DATE = 'drillByDate';

    static $REPORT_TYPE_MAP = [
        self::REPORT_TYPE_KEY_AD_TAG => [self::AD_NETWORK_PARTNER_PULSE_POINT],
        self::REPORT_TYPE_KEY_DAILY => [self::AD_NETWORK_PARTNER_PULSE_POINT]
    ];

    static $BREAKDOWN_MAP = [
        self::REPORT_TYPE_KEY_DAILY => [self::BREAK_DOWN_KEY_DAY],
        self::REPORT_TYPE_KEY_AD_TAG => [self::BREAK_DOWN_KEY_DAY, self::BREAK_DOWN_KEY_SITE, self::BREAK_DOWN_KEY_COUNTRY]
    ];

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="reportType")
     * @Rest\QueryParam(name="breakDown")
     * @Rest\QueryParam(name="drillByAdTag", nullable=true)
     * @Rest\QueryParam(name="drillByDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
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

        if (!$adNetworkPartner instanceof AdNetworkPartner) {
            return $this->view('Not found that AdNetwork Partner', Codes::HTTP_NOT_FOUND);
        }

        /* validate reportType and breakDown */
        $reportType = $request->query->get('reportType', null);
        $breakDown = $request->query->get('breakDown', null);
        if (!$this->isSupportedReport($adNetworkPartner, $reportType, $breakDown)) {
            return $this->view('Not support that breakDown as ' . $breakDown, Codes::HTTP_BAD_REQUEST);
        }

        /* validate and get Publisher */
        $user = $this->getUser();
        $publisher = $user;

        if ($user instanceof AdminInterface) {
            $publisherId = $request->query->get('publisher', null);
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                return $this->view('Not found that publisher', Codes::HTTP_NOT_FOUND);
            }
        }

        $publisherPartners = $adNetworkPartner->getPublisherPartners()->toArray();

        $existedPublisherIds = array_map(function (PublisherPartner $publisherPartner) {
            return $publisherPartner->getPublisherId();
        }, $publisherPartners);

        if (!in_array($publisher->getId(), $existedPublisherIds)) {
            return $this->view('That AdNetwork is not a partner of this Publisher', Codes::HTTP_BAD_REQUEST);
        }

        return $this->getResult($this->getReports($publisher, $reportType, $breakDown));
    }

    /**
     * check if supported breakDown
     * @param AdNetworkPartner $adNetworkPartner
     * @param $reportType
     * @param $breakDown
     * @return bool|ReportTypeInterface
     */
    private function isSupportedReport(AdNetworkPartner $adNetworkPartner, $reportType, $breakDown)
    {
        if (!array_key_exists($reportType, self::$REPORT_TYPE_MAP)
            || !in_array($adNetworkPartner->getNameCanonical(), self::$REPORT_TYPE_MAP[$reportType])
            || !array_key_exists($reportType, self::$BREAKDOWN_MAP)
        ) {
            return false;
        }

        if (!in_array($breakDown, self::$BREAKDOWN_MAP[$reportType])) {
            return false;
        }

        return true;
    }

    /**
     * get Reports by publisher and breakDown
     * @param $publisher
     * @param $reportType
     * @param $breakDown
     * @return array
     * @throws NotSupportedException
     */
    private function getReports($publisher, $reportType, $breakDown)
    {
        if (self::REPORT_TYPE_KEY_AD_TAG === $reportType) {
            if (self::BREAK_DOWN_KEY_DAY === $breakDown) {
                return $this->getAccountManagementReport($publisher);
            }

            if (self::BREAK_DOWN_KEY_SITE === $breakDown) {
                return $this->getDomainImpressionReport($publisher);
            }

            if (self::BREAK_DOWN_KEY_COUNTRY === $breakDown) {
                return $this->getCountryDailyReport($publisher);
            }
        }

        if (self::REPORT_TYPE_KEY_DAILY === $reportType) {
            if (self::BREAK_DOWN_KEY_DAY === $breakDown) {
                return $this->getDailyReport($publisher);
            }
        }

        throw new NotSupportedException(sprintf('Not support that reportType-breakDown as %s-%s', $reportType, $breakDown));
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getAccountManagementReport(PublisherInterface $publisher)
    {
        // check if has drill down for Ad Tag
        $drillParams = $this->getDrillParams();
        if ($this->hasDrillParams($drillParams)) {
            return $this->getAdTagDomainImpressionReportAction($publisher, $drillParams);
        }

        return $this->getReportSelectorService()->getReports(new AccountManagementReportType($publisher, $tagId = null), $this->getParams());
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getDailyReport(PublisherInterface $publisher)
    {
        return $this->getReportSelectorService()->getReports(new DailyReportType($publisher, $date = new \DateTime()), $this->getParams());
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getDomainImpressionReport(PublisherInterface $publisher)
    {
        return $this->getReportSelectorService()->getReports(new DomainImpressionReportType($publisher, $domain = null), $this->getParams());
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getCountryDailyReport(PublisherInterface $publisher)
    {
        return $this->getReportSelectorService()->getReports(new CountryDailyReportType($publisher, $country = null, $tagId = null), $this->getParams());
    }

    /**
     * @param PublisherInterface $publisher
     * @param array|null $drillParams
     * @return mixed
     */
    private function getAdTagDomainImpressionReportAction(PublisherInterface $publisher, array $drillParams = null)
    {
        return $this->getReportSelectorService()->getReports(new AdTagDomainImpressionReportType($publisher, $adTag = $drillParams[self::PARAM_DRILL_BY_AD_TAG], $domain = null, $date = $drillParams[self::PARAM_DRILL_BY_DATE]), $this->getParams());
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
     * create params from query params of request, only startDate + endDate + group
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
     * check if has drill params
     * @param array $drillParams
     * @return bool
     */
    private function hasDrillParams(array $drillParams)
    {
        return (isset($drillParams[self::PARAM_DRILL_BY_AD_TAG])
            || isset($drillParams[self::PARAM_DRILL_BY_DATE])
        );
    }

    /**
     * @return array
     */
    private function getDrillParams()
    {
        $allParams = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        $allowedParams = array_fill_keys([
            self::PARAM_DRILL_BY_AD_TAG,
            self::PARAM_DRILL_BY_DATE
        ], null);

        $drillParams = array_intersect_key($allParams, $allowedParams);
        if (isset($drillParams[self::PARAM_DRILL_BY_DATE])) {
            $dateUtil = $this->get('tagcade.service.date_util');
            $drillParams[self::PARAM_DRILL_BY_DATE] = $dateUtil->getDateTime($drillParams[self::PARAM_DRILL_BY_DATE], false);
        }

        return $drillParams;
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

    /**
     * @return \Tagcade\Service\Report\UnifiedReport\Selector\ReportSelector
     */
    private function getReportSelectorService()
    {
        return $this->get('tagcade.service.report.unified_report.selector.report_selector');
    }
}
