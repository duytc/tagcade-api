<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class AdNetwork implements ReportTypeInterface
{
    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;

    public function __construct(AdNetworkInterface $adNetwork)
    {
        $this->adNetwork = $adNetwork;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }
}