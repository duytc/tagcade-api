<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators;


use Tagcade\Domain\DTO\Report\RtbReport\RtbRedisReportDataInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\RtbReport\ReportInterface;

abstract class RtbSnapshotCreatorAbstract extends RtbCreatorAbstract implements RtbSnapshotCreatorInterface
{
    const RESULT_KEY_SLOT_OPPORTUNITY = 'opportunities';
    const RESULT_KEY_IMPRESSION = 'impressions';
    const RESULT_KEY_PRICE = 'price';

    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        $result = array(
            self::RESULT_KEY_SLOT_OPPORTUNITY => 0,
            self::RESULT_KEY_IMPRESSION => 0,
            self::RESULT_KEY_PRICE => 0,
        );

        foreach ($redisReportData as $id => $reportData) {
            if (!$reportData instanceof RtbRedisReportDataInterface) {
                throw new LogicException('Expect RedisReportDataInterface');
            }

            $this->aggregateAdSlotReportData($result, $reportData);
        }

        $this->constructReportModel($report, $result);
    }

    /**
     * build report from data array
     *
     * @param ReportInterface $report
     * @param array $data
     * @return mixed
     */
    abstract protected function constructReportModel(ReportInterface $report, array $data);

    protected function aggregateAdSlotReportData(array &$result, RtbRedisReportDataInterface $adSlotReportCount)
    {
        $result[self::RESULT_KEY_SLOT_OPPORTUNITY] += $adSlotReportCount->getSlotOpportunities();
        $result[self::RESULT_KEY_IMPRESSION] += $adSlotReportCount->getImpressions();
        $result[self::RESULT_KEY_PRICE] += $adSlotReportCount->getPrice();
    }
}