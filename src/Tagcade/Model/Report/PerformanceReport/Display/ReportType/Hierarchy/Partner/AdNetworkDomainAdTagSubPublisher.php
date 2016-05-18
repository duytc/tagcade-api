<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AdNetworkDomainAdTagSubPublisher extends AbstractCalculatedReportType
{
    const REPORT_TYPE = 'partner.adNetworkDomainAdTagSubPublisher';

    /** @var string the domain of site */
    private $domain;

    /** @var string */
    private $partnerTagId;

    /** @var AdNetworkInterface */
    private $adNetwork;

    /** @var SubPublisherInterface */
    private $subPublisher;

    public function __construct(AdNetworkInterface $adNetwork = null, $domain = null, $partnerTagId = null, SubPublisherInterface $subPublisher)
    {
        $this->adNetwork = $adNetwork instanceof AdNetworkInterface ? $adNetwork : null;
        $this->domain = $domain;
        $this->partnerTagId = $partnerTagId;
        $this->subPublisher = $subPublisher;
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

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getPartnerTagId()
    {
        return $this->partnerTagId;
    }

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkDomainAdTagSubPublisherReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not supported
    }
}