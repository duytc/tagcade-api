<?php

namespace Tagcade\Service\UnifiedReportImporter;


use Tagcade\Model\Report\UnifiedReport\CommonReport;

interface UnifiedReportGeneratorInterface
{
    /**
     * @param CommonReport[] $reports
     * @return array
     */
    public function generateNetworkReports(array $reports);

    /**
     * generate AccountSiteAdTag reports from common reports
     * @param CommonReport[] $reports
     * @return array
     */
    public function generateNetworkAdTagReports(array $reports);

    /**
     * @param array $reports
     * @return mixed
     */
    public function generateNetworkDomainAdTagReports(array $reports);

    /**
     * @param array $reports
     * @return mixed
     */
    public function generateNetworkDomainAdTagForSubPublisherReports(array $reports);

    /**
     * generate AccountSite reports from common reports
     * @param CommonReport[] $reports
     *
     * @return array
     */
    public function generateNetworkSiteReports(array $reports);

    /**
     * generate AccountAdTag reports from common reports
     * @param CommonReport[] $reports
     * @return array
     */
    public function generatePublisherReport(array $reports);

    /**
     * @param CommonReport[] $reports
     * @return array
     */
    public function generateSubPublisherReport(array $reports);

    /**
     * @param CommonReport[] $reports
     * @return mixed
     */
    public function generateSubPublisherNetworkReport(array $reports);

    /**
     * @param array $reports
     * @return mixed
     */
    public function generateNetworkSiteForSubPublisherReports(array $reports);

    /**
     * @param array $reports
     * @return mixed
     */
    public function generateNetworkAdTagForSubPublisherReports(array $reports);
}