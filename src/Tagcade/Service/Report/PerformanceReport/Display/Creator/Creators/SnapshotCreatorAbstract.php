<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators;

use Tagcade\Domain\DTO\Report\Performance\AdSlotReportCount;
use Tagcade\Domain\DTO\Report\Performance\AdTagReportCount;
use Tagcade\Domain\DTO\Report\Performance\BaseAdSlotReportCountInterface;
use Tagcade\Domain\DTO\Report\Performance\BaseAdTagReportCountInterface;
use Tagcade\Domain\DTO\Report\Performance\RedisReportDataInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

abstract class SnapshotCreatorAbstract extends CreatorAbstract implements SnapshotCreatorInterface {

    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        $result = array(
            self::CACHE_KEY_SLOT_OPPORTUNITY => 0,
            self::CACHE_KEY_RTB_IMPRESSION => 0,
            self::CACHE_KEY_OPPORTUNITY => 0,
            self::CACHE_KEY_FIRST_OPPORTUNITY => 0,
            self::CACHE_KEY_IMPRESSION => 0,
            self::CACHE_KEY_VERIFIED_IMPRESSION => 0,
            self::CACHE_KEY_UNVERIFIED_IMPRESSION => 0,
            self::CACHE_KEY_BLANK_IMPRESSION => 0,
            self::CACHE_KEY_VOID_IMPRESSION => 0,
            self::CACHE_KEY_CLICK => 0,
            self::CACHE_KEY_PASSBACK => 0,
            self::CACHE_KEY_FORCED_PASSBACK => 0,
        );

        foreach ($redisReportData as $id => $reportData) {
            if (!$reportData instanceof RedisReportDataInterface) {
                throw new LogicException('Expect RedisReportDataInterface');
            }

            $this->aggregateAdTagReportData($result, $reportData);
            $this->aggregateAdSlotReportData($result, $reportData);
        }

        $this->constructReportModel($report, $result);
    }

    abstract protected function constructReportModel(ReportInterface $report, array $data);

    protected function aggregateAdTagReportData(array &$result, RedisReportDataInterface $adTagReportCount)
    {
        if (!$adTagReportCount instanceof BaseAdTagReportCountInterface) {
            return;
        }

        $result[self::CACHE_KEY_OPPORTUNITY] += $adTagReportCount->getOpportunities();
        $result[self::CACHE_KEY_FIRST_OPPORTUNITY] += $adTagReportCount->getFirstOpportunityCount();
        $result[self::CACHE_KEY_IMPRESSION] += $adTagReportCount->getImpressions();
        $result[self::CACHE_KEY_VERIFIED_IMPRESSION] += $adTagReportCount->getVerifiedImpressionCount();
        $result[self::CACHE_KEY_UNVERIFIED_IMPRESSION] += $adTagReportCount->getUnverifiedImpressionCount();
        $result[self::CACHE_KEY_BLANK_IMPRESSION] += $adTagReportCount->getBlankImpressionCount();
        $result[self::CACHE_KEY_VOID_IMPRESSION] += $adTagReportCount->getVoidImpressionCount();
        $result[self::CACHE_KEY_CLICK] += $adTagReportCount->getClickCount();
        $result[self::CACHE_KEY_PASSBACK] += $adTagReportCount->getPassbackCount();
        $result[self::CACHE_KEY_FORCED_PASSBACK] += $adTagReportCount->getForcedPassbacks();
    }

    protected function aggregateAdSlotReportData(array &$result, RedisReportDataInterface $adSlotReportCount)
    {
        if (!$adSlotReportCount instanceof BaseAdSlotReportCountInterface) {
            return;
        }

        $result[self::CACHE_KEY_SLOT_OPPORTUNITY] += $adSlotReportCount->getSlotOpportunities();
        $result[self::CACHE_KEY_RTB_IMPRESSION] += $adSlotReportCount->getRtbImpression();
    }
} 