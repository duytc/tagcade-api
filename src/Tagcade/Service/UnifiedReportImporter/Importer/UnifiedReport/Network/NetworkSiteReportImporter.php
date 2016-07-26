<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network;


use Tagcade\Entity\Report\UnifiedReport\Network\NetworkSiteReport;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class NetworkSiteReportImporter extends ImporterAbstract implements NetworkSiteReportImporterInterface
{

    /**
     * @var NetworkSiteReportRepositoryInterface
     */
    private $networkSiteReportRepository;

    /**
     * NetworkSiteReportImporter constructor.
     * @param NetworkSiteReportRepositoryInterface $networkSiteReportRepository
     */
    public function __construct(NetworkSiteReportRepositoryInterface $networkSiteReportRepository)
    {
        $this->networkSiteReportRepository = $networkSiteReportRepository;
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
            return $report instanceof NetworkSiteReport;
        });

        return $this->networkSiteReportRepository->saveMultipleReport($reports, $override, $this->getBatchSize());
    }
}