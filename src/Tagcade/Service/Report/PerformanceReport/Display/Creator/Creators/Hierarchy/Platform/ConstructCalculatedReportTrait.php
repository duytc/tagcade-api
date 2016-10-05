<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

trait ConstructCalculatedReportTrait {
    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof CalculatedReportInterface) {
            throw new InvalidArgumentException('Expect instance CalculatedReportInterface');
        }

        $report->setTotalOpportunities($data[SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY])
            ->setSlotOpportunities($data[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY])
            ->setHbRequests($data[SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST])
            ->setImpressions($data[SnapshotCreatorInterface::CACHE_KEY_IMPRESSION])
            ->setRtbImpressions($data[SnapshotCreatorInterface::CACHE_KEY_RTB_IMPRESSION])
            ->setPassbacks($data[SnapshotCreatorInterface::CACHE_KEY_PASSBACK])
            ->setFillRate()
        ;

        if (isset($data[PlatformSnapshot::BILLED_AMOUNT])) {
            $report->setBilledAmount($data[PlatformSnapshot::BILLED_AMOUNT]);
        }

        if (isset($data[PlatformSnapshot::BILLED_RATE])) {
            $report->setBilledRate($data[PlatformSnapshot::BILLED_RATE]);
        }

        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}