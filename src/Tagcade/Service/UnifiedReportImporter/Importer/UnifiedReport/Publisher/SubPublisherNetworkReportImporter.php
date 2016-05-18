<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Publisher;


use Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherNetworkReport;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherNetworkReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class SubPublisherNetworkReportImporter extends ImporterAbstract implements SubPublisherNetworkReportImporterInterface
{
    /**
     * @var SubPublisherNetworkReportRepositoryInterface
     */
    private $subPublisherNetworkReportRepository;

    /**
     * SubPublisherNetworkReportImporter constructor.
     * @param SubPublisherNetworkReportRepositoryInterface $subPublisherNetworkReportRepository
     */
    public function __construct(SubPublisherNetworkReportRepositoryInterface $subPublisherNetworkReportRepository)
    {
        $this->subPublisherNetworkReportRepository = $subPublisherNetworkReportRepository;
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
            return $report instanceof SubPublisherNetworkReport;
        });

        $this->subPublisherNetworkReportRepository->saveMultipleReport($reports, $this->getBatchSize());

        return true;
    }
}