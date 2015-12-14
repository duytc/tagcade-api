<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\UnifiedReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

interface CountryDailyRepositoryInterface extends UnifiedReportRepositoryInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getReportsForAdTagGroupCountry(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10);

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getReportsForAdTagCountry(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10);
}