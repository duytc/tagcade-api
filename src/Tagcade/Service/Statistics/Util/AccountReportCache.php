<?php

namespace Tagcade\Service\Statistics\Util;

use Tagcade\Cache\Legacy\Cache\RedisCache;
use Tagcade\Domain\DTO\Statistics\Dashboard\AdminDashboard;
use Tagcade\Domain\DTO\Statistics\Dashboard\PublisherDashboard;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\PlatformStatistics;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AccountReport;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\PlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface as VideoReportInterface;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\PlatformReport as VideoPlatformReport;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport as VideoAccountReport;

class AccountReportCache implements AccountReportCacheInterface
{
    const ACCOUNT_TEMPLATE = 'account_report_%s_%s';
    const PLATFORM_TEMPLATE = 'platform_report_%s';
    const VIDEO_ACCOUNT_TEMPLATE = 'video_account_report_%s_%s';
    const VIDEO_PLATFORM_TEMPLATE = 'video_platform_report_%s';
    const DATE_TIME_TEMPLATE = 'Y-m-d';
    const DATE_TIME_TEMPLATE_WITH_HOUR = 'Y-m-d_G';
    const LIFE_TIME = 259200; //3 days

    /** @var RedisCache */
    private $redisCache;

    /**
     * AccountReportCache constructor.
     * @param RedisCache $redisCache
     */
    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherDashboardHourlyFromRedis(PublisherInterface $publisher, $date = null)
    {
        $publisherId = $publisher->getId();
        if (empty($date)) {
            $date = date_create('now');
        }

        $accountReports = [];

        // on dashboard chart will only display from 0 to current hour
        $currentHour = (new \DateTime())->format('G');
        for ($i = 0; $i <= $currentHour; $i++) {
            $dateWithHour = $date->setTime($i, 0);
            $key = sprintf(self::ACCOUNT_TEMPLATE, $publisherId, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));

            if ($this->redisCache->contains($key)) {
                $accountReports[] = $this->redisCache->fetch($key);
            }
        }

        return $accountReports;
    }

    /**
     * @inheritdoc
     */
    public function getPlatformDashboardHourlyFromRedis($date = null)
    {
        if (empty($date)) {
            $date = date_create('now');
        }

        $platformReports = [];
        // on dashboard chart will only display from 0 to current hour
        $currentHour = (new \DateTime())->format('G');
        for ($i = 0; $i <= $currentHour; $i++) {
            $dateWithHour = $date->setTime($i, 0);
            $key = sprintf(self::PLATFORM_TEMPLATE, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));

            if ($this->redisCache->contains($key)) {
                $platformReports[] = $this->redisCache->fetch($key);
            }
        }

        return $platformReports;
    }

    /**
     * @inheritdoc
     */
    public function getVideoPublisherDashboardHourlyFromRedis(PublisherInterface $publisher, $date, $currentHour = null)
    {
        $publisherId = $publisher->getId();
        if (!$date instanceof \DateTime) {
            $date = date_create('now');
        }

        $accountReports = [];

        // on dashboard chart will only display from 0 to current hour
        if (empty($currentHour)) {
            $currentHour = (new \DateTime())->format('G');
        }

        for ($i = 0; $i <= $currentHour; $i++) {
            $dateWithHour = $date->setTime($i, 0);
            $key = sprintf(self::VIDEO_ACCOUNT_TEMPLATE, $publisherId, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));

            if ($this->redisCache->contains($key)) {
                $accountReports[] = $this->redisCache->fetch($key);
            }
        }

        return $accountReports;
    }

    /**
     * @inheritdoc
     */
    public function getVideoPlatformDashboardHourlyFromRedis($date = null)
    {
        if (empty($date)) {
            $date = date_create('now');
        }

        $platformReports = [];
        // on dashboard chart will only display from 0 to current hour
        $currentHour = (new \DateTime())->format('G');
        for ($i = 0; $i <= $currentHour; $i++) {
            $dateWithHour = $date->setTime($i, 0);
            $key = sprintf(self::VIDEO_PLATFORM_TEMPLATE, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));

            if ($this->redisCache->contains($key)) {
                $platformReports[] = $this->redisCache->fetch($key);
            }
        }

        return $platformReports;
    }

    /**
     * @param $reports
     * @return mixed
     */
    public function saveHourReports($reports = [])
    {
        foreach ($reports as $report) {
            if (!($report instanceof ReportInterface || $report instanceof VideoReportInterface)) {
                continue;
            }

            if ($report instanceof VideoReportInterface) {
                $report->setSubReports([]);
            }

            $key = $this->buildCacheKey($report, $report->getDate());

            if (!empty($key)) {
                $this->redisCache->save($key, $report, self::LIFE_TIME);
            }
        }
    }

    /**
     * @param $reports
     * @return mixed
     */
    public function saveCurrentStatisticReports($reports)
    {
        if (!($reports instanceof PublisherDashboard || $reports instanceof AdminDashboard)) {
            return;
        }

        $key = $this->buildCacheKeyForCurrentStatistic($reports);

        if (!empty($key)) {
            $this->redisCache->save($key, $reports, self::LIFE_TIME);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPublisherDashboardByHourFromRedis(PublisherInterface $publisher, $date = null)
    {
        $publisherId = $publisher->getId();
        if (empty($date)) {
            $date = date_create('now');
        }

        // get report for date with hour
        $accountReport = [];
        $dateWithHour = $date;

        $key = sprintf(self::ACCOUNT_TEMPLATE, $publisherId, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));
        if ($this->redisCache->contains($key)) {
            $accountReport = $this->redisCache->fetch($key);
        }

        return $accountReport;
    }

    /**
     * @inheritdoc
     */
    public function getPlatformDashboardByHourFromRedis($date = null)
    {
        if (empty($date)) {
            $date = date_create('now');
        }

        // get report for data with hour
        $platformReport = [];
        $dateWithHour = $date;

        $key = sprintf(self::PLATFORM_TEMPLATE, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));
        if ($this->redisCache->contains($key)) {
            $platformReport = $this->redisCache->fetch($key);
        }

        return $platformReport;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherDashboardSnapshot(PublisherInterface $publisher, $date = null)
    {
        $publisherId = $publisher->getId();
        if (empty($date)) {
            $date = date_create('now');
        }

        // get report for date with hour
        $accountReport = [];

        $key = sprintf(self::ACCOUNT_TEMPLATE, $publisherId, $date->format(self::DATE_TIME_TEMPLATE));
        if ($this->redisCache->contains($key)) {
            $accountReport = $this->redisCache->fetch($key);
        }

        return $accountReport;
    }

    /**
     * @inheritdoc
     */
    public function getPlatformDashboardSnapshot($date = null)
    {
        if (empty($date)) {
            $date = date_create('now');
        }

        // get report for data with hour
        $platformReport = [];

        $key = sprintf(self::PLATFORM_TEMPLATE, $date->format(self::DATE_TIME_TEMPLATE));
        if ($this->redisCache->contains($key)) {
            $platformReport = $this->redisCache->fetch($key);
        }

        return $platformReport;
    }

    /**
     * @param $report
     * @param \DateTime $dateWithHour
     * @return mixed
     */
    private function buildCacheKey($report, \DateTime $dateWithHour)
    {
        if ($report instanceof AccountReport) {
            return sprintf(self::ACCOUNT_TEMPLATE, $report->getPublisherId(), $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));
        }

        if ($report instanceof PlatformReport) {
            return sprintf(self::PLATFORM_TEMPLATE, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));
        }

        if ($report instanceof VideoAccountReport) {
            return sprintf(self::VIDEO_ACCOUNT_TEMPLATE, $report->getPublisherId(), $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));
        }

        if ($report instanceof VideoPlatformReport) {
            return sprintf(self::VIDEO_PLATFORM_TEMPLATE, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));
        }

        return null;
    }

    /**
     * @param $report
     * @return mixed
     */
    private function buildCacheKeyForCurrentStatistic($report)
    {
        if ($report instanceof PublisherDashboard && $report->getAccountStatistics() instanceof AccountStatistics) {
            $accountStatisticsReports = $report->getAccountStatistics()->getReports();
            if (!is_array($accountStatisticsReports) || empty($accountStatisticsReports)) {
                return null;
            }

            $accountStatisticsReport = $accountStatisticsReports[0];
            if (!$accountStatisticsReport instanceof AccountReport) {
                return null;
            }

            return sprintf(self::ACCOUNT_TEMPLATE, $accountStatisticsReport->getPublisherId(), $accountStatisticsReport->getDate()->format(self::DATE_TIME_TEMPLATE));
        }

        if ($report instanceof AdminDashboard && $report->getPlatformStatistics() instanceof PlatformStatistics) {
            $platformStatisticsReports = $report->getPlatformStatistics()->getReports();
            if (!is_array($platformStatisticsReports) || empty($platformStatisticsReports)) {
                return null;
            }

            $platformStatisticsReport = $platformStatisticsReports[0];
            if (!$platformStatisticsReport instanceof PlatformReport) {
                return null;
            }

            return sprintf(self::PLATFORM_TEMPLATE, $platformStatisticsReport->getDate()->format(self::DATE_TIME_TEMPLATE));
        }

        return null;
    }
}