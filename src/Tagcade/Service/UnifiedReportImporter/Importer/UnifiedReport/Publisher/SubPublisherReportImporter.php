<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Publisher;


use Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherReport;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class SubPublisherReportImporter extends ImporterAbstract implements SubPublisherReportImporterInterface
{
    /**
     * @var SubPublisherReportRepositoryInterface
     */
    private $subPublisherReportRepository;

    /**
     * SubPublisherReportImporter constructor.
     * @param SubPublisherReportRepositoryInterface $subPublisherReportRepository
     */
    public function __construct(SubPublisherReportRepositoryInterface $subPublisherReportRepository)
    {
        $this->subPublisherReportRepository = $subPublisherReportRepository;
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
            return $report instanceof SubPublisherReport;
        });

        $this->subPublisherReportRepository->saveMultipleReport($reports, $override, $this->getBatchSize());

        return true;
    }
}