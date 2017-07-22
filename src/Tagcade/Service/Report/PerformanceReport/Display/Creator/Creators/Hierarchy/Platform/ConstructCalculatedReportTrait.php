<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

trait ConstructCalculatedReportTrait
{
    use CalculateAdOpportunitiesTrait;

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof CalculatedReportInterface) {
            throw new InvalidArgumentException('Expect instance CalculatedReportInterface');
        }

        $report->setTotalOpportunities($data[SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY])
            ->setSlotOpportunities($data[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[SnapshotCreatorInterface::CACHE_KEY_IMPRESSION])
            ->setPassbacks($data[SnapshotCreatorInterface::CACHE_KEY_PASSBACK])
            ->setInBannerRequests(array_key_exists(SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST, $data) ? $data[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST] : 0)
            ->setInBannerTimeouts(array_key_exists(SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT, $data) ? $data[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT] : 0)
            ->setInBannerImpressions(array_key_exists(SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION, $data) ? $data[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION] : 0)
            ->setFillRate()
            ->setAdOpportunities($this->calculateAdOpportunities($report->getTotalOpportunities(), $report->getPassbacks()));

        if (isset($data[PlatformSnapshot::BILLED_AMOUNT])) {
            $report->setBilledAmount($data[PlatformSnapshot::BILLED_AMOUNT]);
        }

        if (isset($data[PlatformSnapshot::IN_BANNER_BILLED_AMOUNT])) {
            $report->setInBannerBilledAmount($data[PlatformSnapshot::IN_BANNER_BILLED_AMOUNT]);
        }

        if (isset($data[PlatformSnapshot::BILLED_RATE])) {
            $report->setBilledRate($data[PlatformSnapshot::BILLED_RATE]);
        }

        if (isset($data[PlatformSnapshot::IN_BANNER_BILLED_RATE])) {
            $report->setInBannerBilledRate($data[PlatformSnapshot::IN_BANNER_BILLED_RATE]);
        }

        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}