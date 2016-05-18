<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Publisher;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Publisher\SubPublisherNetworkReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherNetwork extends AbstractReportType
{
    const REPORT_TYPE = 'unified.subpublisher.network';
    /**
     * @var SubPublisherInterface
     */
    private $subPublisher;
    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;

    /**
     * Account constructor.
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     */
    public function __construct(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher)
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

    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    public function getAdNetworkId()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            return $this->adNetwork->getId();
        }

        return null;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SubPublisherNetworkReport;
    }
}