<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

interface ReportBuilderInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllDemandPartnersByPartnerReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllDemandPartnersByDayReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllDemandPartnersBySiteReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllDemandPartnersByAdTagReport(PublisherInterface $publisher, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getPartnerAllSitesByDayReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param SubPublisherInterface $publisher
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getPartnerAllSitesByDayForSubPublisherReport(SubPublisherInterface $publisher, AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getPartnerAllSitesBySitesReport(PublisherInterface $publisher, AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getPartnerAllSitesByAdTagsReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param SubPublisherInterface $subPublisher
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getPartnerByAdTagsForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, Params $params);


    /**
     * @param AdNetworkInterface $adNetwork
     * @param $domain
     * @param Params $params
     * @return mixed
     */
    public function getPartnerSiteByDaysReport(AdNetworkInterface $adNetwork, $domain, Params $params);

    public function getPartnerSiteByDaysForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, $domain, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return mixed
     */
    public function getPartnerSiteByAdTagsReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * @param SubPublisherInterface $subPublisher
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return mixed
     */
    public function getPartnerSiteByAdTagsForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getSubPublishersReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllDemandPartnersByDayDiscrepancyReport(PublisherInterface $publisher, Params $params);

    /**
     * get discrepancy of all partners breakdown by partner for a Publisher between unified report and tagcade performance report
     * This is used for comparing with Unified report 'all partners by day for an account'
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllPartnersDiscrepancyByPartnerForPublisher(PublisherInterface $publisher, Params $params);

    /**
     * get discrepancy of all partners breakdown by day for a Publisher between unified report and tagcade performance report
     * This is used for comparing with Unified report 'all partners by day for an account'
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllPartnersDiscrepancyByDayForPublisher(PublisherInterface $publisher, Params $params);

    /**
     * get discrepancy of all partners breakdown by domain for a Publisher between unified report and tagcade performance report
     * This is used for comparing with Unified report 'all partners by domain for an account'
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllPartnersDiscrepancyBySiteForPublisher(PublisherInterface $publisher, Params $params);

    /**
     * get discrepancy of all partners breakdown by ad tag for a Publisher between unified report and tagcade performance report
     * This is used for comparing with Unified report 'all partners by ad tag for an account'
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllPartnersDiscrepancyByAdTagForPublisher(PublisherInterface $publisher, Params $params);

    /**
     * get discrepancy of all sites breakdown by day For a Partner between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partner by day'
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesDiscrepancyByDayForPartner(AdNetworkInterface $adNetwork, Params $params);

    /**
     * get discrepancy of all sites breakdown by day For a Partner with a SubPublisher between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partner by day'
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesDiscrepancyByDayForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params);

    /**
     * get discrepancy of all sites breakdown by domain For a Partner between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partners by domain'
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesDiscrepancyBySiteForPartner(AdNetworkInterface $adNetwork, Params $params);

    /**
     * get discrepancy of all sites breakdown by domain For a Partner with a SubPublisher between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partners by domain'
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesDiscrepancyBySiteForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params);

    /**
     * get discrepancy of all sites breakdown by ad tag For a Partner between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partners by ad tag'
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesDiscrepancyByAdTagForPartner(AdNetworkInterface $adNetwork, Params $params);

    /**
     * get discrepancy of all sites breakdown by ad tag For a Partner with a SubPublisher between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partners by ad tag'
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesDiscrepancyByAdTagForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params);

    /**
     * get discrepancy of a site breakdown by day For a Partner between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partner by day'
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return mixed
     */
    public function getSiteDiscrepancyByDayForPartner(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * get discrepancy of a site breakdown by ad tag For a Partner between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partners by ad tag'
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return mixed
     */
    public function getSiteDiscrepancyByAdTagForPartner(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * get discrepancy of a site breakdown by ad tag For a Partner with a SubPublisher between unified report and tagcade performance report
     * This is used for comparing with Unified report 'a partners by ad tag'
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param SubPublisherInterface $subPublisher
     * @param Params $params
     * @return mixed
     */
    public function getSiteDiscrepancyByAdTagForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, SubPublisherInterface $subPublisher, Params $params);
}