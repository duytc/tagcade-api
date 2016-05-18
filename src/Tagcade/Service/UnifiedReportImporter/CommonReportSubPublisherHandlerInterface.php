<?php

namespace Tagcade\Service\UnifiedReportImporter;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\CommonReport;

interface CommonReportSubPublisherHandlerInterface
{
    /**
     * @param array $rawReports
     * @param AdNetworkInterface $adNetwork
     * @param $override
     * @return CommonReport[]
     */
    public function generateCommonReports(AdNetworkInterface $adNetwork, array $rawReports, $override);
}