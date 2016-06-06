<?php

namespace Tagcade\Service\Report\UnifiedReport\Exporter;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

interface ReportExporterInterface
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
     * @param PublisherInterface $publisher
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getPartnerAllSitesByAdTagsReport(PublisherInterface $publisher, AdNetworkInterface $adNetwork, Params $params);

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

    /**
     * @param SubPublisherInterface $subPublisher
     * @param AdNetworkInterface $adNetwork
     * @param $domain
     * @param Params $params
     * @return mixed
     */
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
    public function getSubPublisherReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getSubPublishersForPartnerReport(PublisherInterface $publisher, AdNetworkInterface $adNetwork, Params $params);

}