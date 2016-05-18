<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network;


use Tagcade\Entity\Report\UnifiedReport\Network\NetworkSiteSubPublisherReport;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteSubPublisherReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class NetworkSiteSubPublisherReportImporter extends ImporterAbstract implements NetworkSiteSubPublisherReportImporterInterface
{
    /** @var NetworkSiteSubPublisherReportRepositoryInterface */
    private $networkSiteSubPublisherReportRepository;

    /**
     * NetworkSiteReportImporter constructor.
     * @param NetworkSiteSubPublisherReportRepositoryInterface $networkSiteSubPublisherReportRepository
     */
    public function __construct(NetworkSiteSubPublisherReportRepositoryInterface $networkSiteSubPublisherReportRepository)
    {
        $this->networkSiteSubPublisherReportRepository = $networkSiteSubPublisherReportRepository;
    }

    /**
     * import Reports, if $aggregate = true then this report will be merged with the existing report
     * which has the same parameters, site e.g
     * @param array $reports
     * @param $override = false
     *
     * @return bool
     */
    public function importReports(array $reports, $override = false)
    {
        // filter supported Reports
        $reports = array_filter($reports, function ($report) {
            return $report instanceof NetworkSiteSubPublisherReport;
        });

        $this->networkSiteSubPublisherReportRepository->saveMultipleReport($reports, $this->getBatchSize());

        return true;
    }
}