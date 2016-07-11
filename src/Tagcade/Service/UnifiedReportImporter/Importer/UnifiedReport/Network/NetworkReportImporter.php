<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network;


use Tagcade\Entity\Report\UnifiedReport\Network\NetworkReport;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class NetworkReportImporter extends ImporterAbstract implements NetworkReportImporterInterface
{
    /**
     * @var NetworkReportRepositoryInterface
     */
    private $networkReportRepository;

    /**
     * NetworkReportImporter constructor.
     * @param NetworkReportRepositoryInterface $networkReportRepository
     */
    public function __construct(NetworkReportRepositoryInterface $networkReportRepository)
    {
        $this->networkReportRepository = $networkReportRepository;
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
            return $report instanceof NetworkReport;
        });

        $this->networkReportRepository->saveMultipleReport($reports, $this->getBatchSize());

        return true;
    }
}