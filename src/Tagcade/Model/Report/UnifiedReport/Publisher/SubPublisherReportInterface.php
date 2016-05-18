<?php


namespace Tagcade\Model\Report\UnifiedReport\Publisher;
use Tagcade\Model\Report\PartnerReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherReportInterface extends ReportInterface, PartnerReportInterface
{
    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    public function getSubPublisherId();
    /**
     * @param SubPublisherInterface $subPublisher
     */
    public function setSubPublisher($subPublisher);
}