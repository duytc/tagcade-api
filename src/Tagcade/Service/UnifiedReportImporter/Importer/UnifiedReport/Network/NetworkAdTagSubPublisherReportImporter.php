<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network;


use Tagcade\Entity\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReport;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class NetworkAdTagSubPublisherReportImporter extends ImporterAbstract implements NetworkAdTagSubPublisherReportImporterInterface
{
    /** @var NetworkAdTagSubPublisherReportRepositoryInterface */
    private $networkAdTagSubPublisherReportRepository;

    /**
     * NetworkSiteReportImporter constructor.
     * @param NetworkAdTagSubPublisherReportRepositoryInterface $networkAdTagSubPublisherReportRepository
     */
    public function __construct(NetworkAdTagSubPublisherReportRepositoryInterface $networkAdTagSubPublisherReportRepository)
    {
        $this->networkAdTagSubPublisherReportRepository = $networkAdTagSubPublisherReportRepository;
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
            return $report instanceof NetworkAdTagSubPublisherReport;
        });

        $this->networkAdTagSubPublisherReportRepository->saveMultipleReport($reports, $override, $this->getBatchSize());

        return true;
    }
}