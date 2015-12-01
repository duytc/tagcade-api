<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\UnifiedReportRepositoryInterface;

interface AdTagDomainImpressionRepositoryInterface extends UnifiedReportRepositoryInterface
{
    /**
     * get Report With Drill Down
     * @param PublisherInterface $publisher
     * @param mixed $adTag is drill down by ad tag
     * @param \DateTime|null $date is drill down by date
     * @return mixed
     */
    public function getReportWithDrillDown(PublisherInterface $publisher, $adTag, $date = null);
}