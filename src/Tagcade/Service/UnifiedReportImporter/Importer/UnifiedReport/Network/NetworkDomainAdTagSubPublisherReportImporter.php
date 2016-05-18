<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network;


use Tagcade\Entity\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReport;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class NetworkDomainAdTagSubPublisherReportImporter extends ImporterAbstract implements NetworkDomainAdTagSubPublisherReportImporterInterface
{
    /** @var NetworkDomainAdTagSubPublisherReportRepositoryInterface */
    private $networkDomainAdTagSubPublisherReportRepository;

    /**
     * NetworkAdTagReportImporter constructor.
     * @param NetworkDomainAdTagSubPublisherReportRepositoryInterface $networkDomainAdTagSubPublisherReportRepository
     */
    public function __construct(NetworkDomainAdTagSubPublisherReportRepositoryInterface $networkDomainAdTagSubPublisherReportRepository)
    {
        $this->networkDomainAdTagSubPublisherReportRepository = $networkDomainAdTagSubPublisherReportRepository;
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
            return $report instanceof NetworkDomainAdTagSubPublisherReport;
        });

        $this->networkDomainAdTagSubPublisherReportRepository->saveMultipleReport($reports, $override, $this->getBatchSize());

        return true;
    }
}