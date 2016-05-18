<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\SubPublisher;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\CalculatedReportTypeInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherAdNetwork extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'subPublisher.subPublisherAdNetwork';

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
     * @param SubPublisherInterface $subPublisher
     */
    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;
    }

    /**
     * @inheritdoc
     */
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