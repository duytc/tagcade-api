<?php


namespace Tagcade\Model\Report\UnifiedReport\Network;
use Tagcade\Model\Report\UnifiedReport\ReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface NetworkDomainAdTagSubPublisherReportInterface extends ReportInterface
{
    /**
     * @return mixed
     */
    public function getDomain();

    /**
     * @param mixed $domain
     * @return self
     */
    public function setDomain($domain);

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @return mixed
     */
    public function getSubPublisherId();

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
     */
    public function setSubPublisher($subPublisher);
}