<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\ReportResultInterface;

interface ReportBuilderInterface
{
    /**
     * @param Params $params
     * @return mixed
     */
    public function getPlatformReport(Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherReport(PublisherInterface $publisher, Params $params);

    /**
     * @param SiteInterface $site
     * @param Params $params
     * @return mixed
     */
    public function getSiteReport(SiteInterface $site, Params $params);

    /**
     * @param Params $params
     * @return mixed
     */
    public function getAllPublishersReport(Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getPublisherSitesReport(PublisherInterface $publisher, Params $params);
}