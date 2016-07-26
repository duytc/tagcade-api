<?php


namespace Tagcade\Service\UnifiedReportImporter;


interface UnifiedReportGrouperInterface
{
    /**
     * @param array $reports
     * @return array
     */
    public function groupNetworkDomainAdTagReports(array $reports);

    /**
     * @param array $reports
     * @return array
     */
    public function groupNetworkDomainReports(array $reports);

    /**
     * @param array $reports
     * @return array
     */
    public function groupNetworkAdTagReports(array $reports);
}