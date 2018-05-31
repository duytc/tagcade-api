<?php

namespace Tagcade\Service\Statistics\Util;

use Tagcade\Model\User\Role\PublisherInterface;

interface AccountReportCacheInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param null $date
     * @return mixed
     */
    public function getPublisherDashboardHourlyFromRedis(PublisherInterface $publisher, $date = null);

    /**
     * @param null $date
     * @return mixed
     */
    public function getPlatformDashboardHourlyFromRedis($date = null);

    /**
     * @param $reports
     * @return mixed
     */
    public function saveHourReports($reports = []);
}