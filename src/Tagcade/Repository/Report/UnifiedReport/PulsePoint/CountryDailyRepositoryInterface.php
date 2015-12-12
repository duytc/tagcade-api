<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\UnifiedReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

interface CountryDailyRepositoryInterface extends UnifiedReportRepositoryInterface
{
    /**
     * get Count For AdTagGroup-Country report
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getCountForAdTagGroupCountry(PublisherInterface $publisher, UnifiedReportParams $params);

    /**
     * get Items For AdTagGroup-Country report
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return mixed
     */
    public function getItemsForAdTagGroupCountry(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10);

    /**
     * get Average Values For AdTagGroup-Country report
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getAverageValuesForAdTagGroupCountry(PublisherInterface $publisher, UnifiedReportParams $params);

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getAverageValuesForAdTagCountry(PublisherInterface $publisher, UnifiedReportParams $params);

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return int
     */
    public function getCountForAdTagCountry(PublisherInterface $publisher, UnifiedReportParams $params);

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getItemsForAdTagCountry(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10);

}