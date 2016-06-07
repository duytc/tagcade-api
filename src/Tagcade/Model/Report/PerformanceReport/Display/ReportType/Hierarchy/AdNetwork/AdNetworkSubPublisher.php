<?php


namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AdNetworkSubPublisher extends AbstractCalculatedReportType
{
    const REPORT_TYPE = 'adNetwork.adNetworkSubPublisher';

    /** @var SubPublisherInterface */
    private $subPublisher;

    /** @var AdNetworkInterface */
    private $adNetwork;

    public function __construct(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork)
    {
        $this->subPublisher = $subPublisher;
        $this->adNetwork = $adNetwork;
    }

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SubPublisherAdNetworkReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not yet supported, re-modify in the future
    }
}