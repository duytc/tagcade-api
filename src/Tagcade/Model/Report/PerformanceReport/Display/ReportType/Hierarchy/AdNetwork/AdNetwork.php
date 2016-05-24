<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdNetwork extends AbstractCalculatedReportType
{
    const REPORT_TYPE = 'adNetwork.adNetwork';

    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;

    /** @var PublisherInterface */
    private $publisher; // using publisher in case adNetwork = null (means all adNetwork for publisher and sum their reports by day)

    public function __construct(AdNetworkInterface $adNetwork = null, $publisher = null)
    {
        $this->adNetwork = $adNetwork;

        if ($publisher instanceof PublisherInterface) {
            $this->publisher = $publisher;
        }
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @return int|null
     */
    public function getAdNetworkId()
    {
        return $this->adNetwork instanceof AdNetworkInterface ? $this->adNetwork->getId() : null;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }
}