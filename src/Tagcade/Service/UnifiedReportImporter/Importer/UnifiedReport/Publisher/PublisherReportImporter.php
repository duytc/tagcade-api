<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Publisher;


use Tagcade\Entity\Report\UnifiedReport\Publisher\PublisherReport;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class PublisherReportImporter extends ImporterAbstract implements PublisherReportImporterInterface
{
    /**
     * @var PublisherReportRepositoryInterface
     */
    private $publisherReportRepository;

    /**
     * PublisherReportImporter constructor.
     * @param PublisherReportRepositoryInterface $publisherReportRepository
     */
    public function __construct(PublisherReportRepositoryInterface $publisherReportRepository)
    {
        $this->publisherReportRepository = $publisherReportRepository;
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
            return $report instanceof PublisherReport;
        });

        $this->publisherReportRepository->saveMultipleReport($reports, $this->getBatchSize());

        return true;
    }
}