<?php


namespace Tagcade\Model\Report\UnifiedReport\Publisher;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PartnerReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherNetworkReportInterface extends ReportInterface, PartnerReportInterface
{
    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    public function getSubPublisherId();

    /**
     * @return AdNetworkInterface|null
     */
    public function getAdNetwork();

    /**
     * @return int|null
     */
    public function getAdNetworkId();

    public function setAdNetwork($adNetwork);

    public function setSubPublisher($subPublisher);
}