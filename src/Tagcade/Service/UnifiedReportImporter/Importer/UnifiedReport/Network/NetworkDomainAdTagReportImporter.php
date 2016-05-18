<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network;


use Tagcade\Entity\Report\UnifiedReport\Network\NetworkDomainAdTagReport;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterAbstract;

class NetworkDomainAdTagReportImporter extends ImporterAbstract implements NetworkDomainAdTagReportImporterInterface
{

    /**
     * @var NetworkDomainAdTagReportRepositoryInterface
     */
    private $networkDomainAdTagReportRepository;

    /**
     * NetworkAdTagReportImporter constructor.
     * @param NetworkDomainAdTagReportRepositoryInterface $networkDomainAdTagReportRepository
     */
    public function __construct(NetworkDomainAdTagReportRepositoryInterface $networkDomainAdTagReportRepository)
    {
        $this->networkDomainAdTagReportRepository = $networkDomainAdTagReportRepository;
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
            return $report instanceof NetworkDomainAdTagReport;
        });

        return $this->networkDomainAdTagReportRepository->saveMultipleReport($reports, $override);
    }
}