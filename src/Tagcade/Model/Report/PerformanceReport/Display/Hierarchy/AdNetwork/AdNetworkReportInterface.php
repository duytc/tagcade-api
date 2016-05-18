<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

interface AdNetworkReportInterface extends CalculatedReportInterface, RootReportInterface, SuperReportInterface
{
    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork();

}
