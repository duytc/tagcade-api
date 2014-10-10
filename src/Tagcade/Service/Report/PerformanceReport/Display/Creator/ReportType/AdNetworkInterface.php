<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\Core\AdNetworkInterface as AdNetworkModelInterface;
use Tagcade\Model\Report\PerformanceReport\Display\AdNetworkReportInterface;

interface AdNetworkInterface extends ReportTypeInterface
{
    /**
     * @param AdNetworkModelInterface $adNetwork
     * @return AdNetworkReportInterface
     */
    public function doCreateReport(AdNetworkModelInterface $adNetwork);
}