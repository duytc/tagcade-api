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

    /**
     * @param $reports
     * @return mixed
     */
    public function saveCurrentStatisticReports($reports);

    /**
     * @param PublisherInterface $publisher
     * @param $date
     * @param null $currentHour
     * @return mixed
     */
    public function getVideoPublisherDashboardHourlyFromRedis(PublisherInterface $publisher, $date, $currentHour = null);

    /**
     * @param null $date
     * @return mixed
     */
    public function getVideoPlatformDashboardHourlyFromRedis($date = null);

    /**
     * @param PublisherInterface $publisher
     * @param null|\DateTime $date
     * @return mixed
     */
    public function getPublisherDashboardByHourFromRedis(PublisherInterface $publisher, $date = null);

    /**
     * @param null|\DateTime $date
     * @return mixed
     */
    public function getPlatformDashboardByHourFromRedis($date = null);

    /**
     * @param PublisherInterface $publisher
     * @param null|\DateTime $date
     * @return mixed
     */
    public function getPublisherDashboardSnapshot(PublisherInterface $publisher, $date = null);

    /**
     * @param null|\DateTime $date
     * @return mixed
     */
    public function getPlatformDashboardSnapshot($date = null);
}