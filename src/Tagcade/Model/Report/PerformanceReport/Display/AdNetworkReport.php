<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Tagcade\Model\Core\AdNetworkInterface;

class AdNetworkReport extends AbstractCalculatedReport implements AdNetworkReportInterface
{
    protected $adNetwork;

    /**
     * @return AdNetworkInterface|null
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return self
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;
        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdTagReportInterface;
    }

    protected function setDefaultName()
    {
        if ($adNetwork = $this->getAdNetwork()) {
            $this->setName($adNetwork->getName());
        }
    }
}