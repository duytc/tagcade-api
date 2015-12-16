<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Core\PublisherPartner;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AccountManagement as AccountManagementReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagDomainImpression as AdTagDomainImpressionReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\CountryDaily as CountryDailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily as DailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\DomainImpression as DomainImpressionReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagCountry as AdTagCountryReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagGroupDaily as AdTagGroupDailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagGroupCountry as AdTagGroupCountryReportType;
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
    const REPORT_TYPE_KEY_AD_TAG = 'pp-ad-tag';
    const REPORT_TYPE_KEY_DAILY = 'pp-daily-stats';
    const REPORT_TYPE_KEY_SITE = 'pp-site';
    const REPORT_TYPE_KEY_AD_TAG_GROUP = 'pp-ad-tag-group';

    /* breakdown types */
    const BREAK_DOWN_KEY_DAY = 'day';
    const BREAK_DOWN_KEY_SITE = 'site';
    const BREAK_DOWN_KEY_COUNTRY = 'country';

    static $BREAKDOWN_MAP = [
        self::REPORT_TYPE_KEY_DAILY => [self::BREAK_DOWN_KEY_DAY],
        self::REPORT_TYPE_KEY_AD_TAG => [self::BREAK_DOWN_KEY_DAY, self::BREAK_DOWN_KEY_SITE, self::BREAK_DOWN_KEY_COUNTRY],
        self::REPORT_TYPE_KEY_SITE => [self::BREAK_DOWN_KEY_DAY],
        self::REPORT_TYPE_KEY_AD_TAG_GROUP => [self::BREAK_DOWN_KEY_DAY, self::BREAK_DOWN_KEY_COUNTRY]
    ];

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\View(serializerGroups={"acc_mng.summary", "revenue_unified_report.summary", "unified_report.summary", "daily.summary", "ad_tag_domain_impression.summary", "domain_impression.summary", "country_daily.summary"})
     *
     * @Rest\Get("/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="reportType")
     * @Rest\QueryParam(name="breakDown")
     * @Rest\QueryParam(name="page")
     * @Rest\QueryParam(name="size", nullable=true)
     * @Rest\QueryParam(name="searchField", nullable=true)
     * @Rest\QueryParam(name="searchKey", nullable=true)
     * @Rest\QueryParam(name="sortField", nullable=true)
     * @Rest\QueryParam(name="orderBy", nullable=true)
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
            return $this->view(sprintf('Not support that reportType-breakDown as %s-%s', $reportType, $breakDown), Codes::HTTP_BAD_REQUEST);
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
        if (!is_array($adNetworkPartner->getReportTypes()) || !in_array($reportType, $adNetworkPartner->getReportTypes())
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
                return $this->getAdTagDomainImpressionReport($publisher);
            }

            if (self::BREAK_DOWN_KEY_COUNTRY === $breakDown) {
                return $this->getAdTagCountryReport($publisher);
            }
        }

        if (self::REPORT_TYPE_KEY_DAILY === $reportType) {
            if (self::BREAK_DOWN_KEY_DAY === $breakDown) {
                return $this->getDailyReport($publisher);
            }
        }

        if (self::REPORT_TYPE_KEY_SITE === $reportType) {
            if (self::BREAK_DOWN_KEY_DAY === $breakDown) {
                return $this->getDomainImpressionReport($publisher);
            }
        }

        if (self::REPORT_TYPE_KEY_AD_TAG_GROUP === $reportType) {
            if (self::BREAK_DOWN_KEY_DAY === $breakDown) {
                return $this->getAdTagGroupDailyReport($publisher);
            }

            if (self::BREAK_DOWN_KEY_COUNTRY === $breakDown) {
                return $this->getAdTagGroupCountryReport($publisher);
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
        return $this->getReportSelectorService()->getReports(new AccountManagementReportType($publisher, $tagId = null), $this->getParams());
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getAdTagGroupDailyReport(PublisherInterface $publisher)
    {
        return $this->getReportSelectorService()->getReports(new AdTagGroupDailyReportType($publisher, $adTagGroup = null), $this->getParams());
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getAdTagGroupCountryReport(PublisherInterface $publisher)
    {
        return $this->getReportSelectorService()->getReports(new AdTagGroupCountryReportType($publisher, $adTagGroup = null), $this->getParams());
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
    private function getAdTagCountryReport(PublisherInterface $publisher)
    {
        return $this->getReportSelectorService()->getReports(new AdTagCountryReportType($publisher, $country = null, $tagId = null), $this->getParams());
    }

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    private function getAdTagDomainImpressionReport(PublisherInterface $publisher)
    {
        return $this->getReportSelectorService()->getReports(new AdTagDomainImpressionReportType($publisher, $adTag = null, $domain = null, $date = new \DateTime()), $this->getParams());
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
            UnifiedReportParams::PARAM_GROUP,
            UnifiedReportParams::PARAM_PAGE,
            UnifiedReportParams::PARAM_SIZE,
            UnifiedReportParams::PARAM_SEARCH_FIELD,
            UnifiedReportParams::PARAM_SEARCH_KEY,
            UnifiedReportParams::PARAM_SORT_FIELD,
            UnifiedReportParams::PARAM_SORT_DIRECTION,
        ], null);

        $params = array_merge($defaultParams, $params);

        $dateUtil = $this->get('tagcade.service.date_util');
        $startDate = $dateUtil->getDateTime($params[UnifiedReportParams::PARAM_START_DATE], true);
        $endDate = $dateUtil->getDateTime($params[UnifiedReportParams::PARAM_END_DATE]);
        $group = $params[UnifiedReportParams::PARAM_GROUP];
        $page = intval($params[UnifiedReportParams::PARAM_PAGE]);
        $size = intval($params[UnifiedReportParams::PARAM_SIZE]);
        $searchField = $params[UnifiedReportParams::PARAM_SEARCH_FIELD];
        $searchKey = $params[UnifiedReportParams::PARAM_SEARCH_KEY];
        $sortField = $params[UnifiedReportParams::PARAM_SORT_FIELD];
        $sortDirection = $params[UnifiedReportParams::PARAM_SORT_DIRECTION];

        return new UnifiedReportParams($startDate, $endDate, $group, $page, $size, $searchField, $searchKey, $sortField, $sortDirection);
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