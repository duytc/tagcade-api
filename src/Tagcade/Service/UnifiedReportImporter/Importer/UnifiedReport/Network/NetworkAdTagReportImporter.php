<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network;


use Tagcade\Entity\Report\UnifiedReport\Network\NetworkAdTagReport;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class NetworkAdTagReportImporter extends ImporterAbstract implements NetworkAdTagReportImporterInterface
{

    /**
     * @var NetworkAdTagReportRepositoryInterface
     */
    private $networkAdTagReportRepository;

    /**
     * NetworkAdTagReportImporter constructor.
     * @param NetworkAdTagReportRepositoryInterface $networkAdTagReportRepository
     */
    public function __construct(NetworkAdTagReportRepositoryInterface $networkAdTagReportRepository)
    {
        $this->networkAdTagReportRepository = $networkAdTagReportRepository;
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
            return $report instanceof NetworkAdTagReport;
        });

        $this->networkAdTagReportRepository->saveMultipleReport($reports, $this->getBatchSize());

        return true;
    }
}