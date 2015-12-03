<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\UnifiedReportRepositoryInterface;

interface CountryDailyRepositoryInterface extends UnifiedReportRepositoryInterface
{
    /**
     * get AdTag Report For a Publisher by a Country in a date range
     * @param PublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getAdTagCountryReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);

    /**
     * get AdTag Group Report For a Publisher by a Country in a date range
     * @param PublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getAdTagGroupCountryReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);
}