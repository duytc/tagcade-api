<?php

namespace Tagcade\Service\Statistics\Util;

use Tagcade\Cache\Legacy\Cache\RedisCache;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AccountReport;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\PlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AccountReportCache implements AccountReportCacheInterface
{
    const ACCOUNT_TEMPLATE = "account_report_%s_%s";
    const PLATFORM_TEMPLATE = "platform_report_%s";
    const DATE_TIME_TEMPLATE_WITH_HOUR = "Y-m-d_G";
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
     * @param $reports
     * @return mixed
     */
    public function saveHourReports($reports = [])
    {
        foreach ($reports as $report) {
            if (!$report instanceof ReportInterface) {
                continue;
            }

            $key = $this->buildCacheKey($report);

            if (!empty($key)) {
                $this->redisCache->save($key, $report, self::LIFE_TIME);
            }
        }
    }

    /**
     * @param ReportInterface $report
     * @return mixed
     */
    private function buildCacheKey(ReportInterface $report)
    {
        $dateWithHour = $report->getDate();
        if ($report instanceof AccountReport) {
            return sprintf(self::ACCOUNT_TEMPLATE, $report->getPublisherId(), $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));
        }

        if ($report instanceof PlatformReport) {
            return sprintf(self::PLATFORM_TEMPLATE, $dateWithHour->format(self::DATE_TIME_TEMPLATE_WITH_HOUR));
        }

        return null;
    }
}