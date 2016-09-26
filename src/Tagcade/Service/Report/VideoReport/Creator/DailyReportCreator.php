<?php

namespace Tagcade\Service\Report\VideoReport\Creator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as DemandPartnerReportType;
use Tagcade\Model\User\Role\PublisherInterface;

class DailyReportCreator
{
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var ReportCreatorInterface
     */
    private $reportCreator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ObjectManager $om, ReportCreatorInterface $reportCreator)
    {
        $this->om = $om;
        $this->reportCreator = $reportCreator;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create all reports and persist them
     *
     * @param PublisherInterface[] $publishers
     * @param VideoDemandPartnerInterface[] $videoDemandPartners
     */
    public function createAndSave(array $publishers, $videoDemandPartners)
    {
        $this->logger->info('Getting platform report');
        $platformReport = $this->reportCreator->getReport(new PlatformReportType($publishers));

        $this->logger->info('Persisting platform report');
        $this->om->persist($platformReport);
        $this->logger->info('flushing then detaching platform report');
        $this->om->flush();
        unset($platformReport);
        $this->logger->info('Finished platform report');
        gc_collect_cycles();

        $this->createDemandPartnerReports($videoDemandPartners);

        $this->om->flush();
        unset($adNetworks);

        $this->logger->info('Finished all network reports');

        gc_collect_cycles();
    }

    public function createReportsForPublishers(array $publishers, array $adNetworks)
    {
        $this->createAccountReports($publishers);

//        $this->createAdNetworkReports($adNetworks);

        $this->om->flush();

    }

//    public function createPlatformReport(DateTime $reportDate)
//    {
//        $report = new PlatformReport();
//        $report->setDate($reportDate);
//
//        $accountReportRepository = $this->om->getRepository(AccountReport::class);
//        if (!$accountReportRepository instanceof AccountReportRepositoryInterface) {
//            throw new \Exception('Invalid repository');
//        }
//        /**
//         * @var AccountReportRepositoryInterface|AbstractReportRepository $accountReportRepository
//         */
//
//        $platformCounts = $accountReportRepository->getAggregatedReportsByDateRange($reportDate, $reportDate);
//
//
//        $accountReports = $accountReportRepository->getReportsByDateRange($reportDate, $reportDate);
//        foreach ($accountReports as $accountReport) {
//            /**
//             * @var AccountReport $accountReport
//             */
//            if ($accountReport->getSuperReport() != null) {
//                throw new \Exception('Something went wrong. Platform report has not been created but the account report already has reference');
//            }
//
//            $accountReport->setSuperReport($report);
//            $report->addSubReport($accountReport);
//        }
//
//        $report->parseData($platformCounts)
//            ->setFillRate()
//            ->setThresholdBilledAmount($chainToSubReports = false) // we don't need to calculate for sub reports
//        ;
//
//        $this->om->persist($report);
//        $this->om->flush();
//    }

    protected function createAccountReports(array $publishers)
    {
        $createdReports = [];
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $accountReport = $this->reportCreator->getReport(
                new AccountReportType($publisher)
            );
            /**
             * @var AccountReport $accountReport
             */
            $this->om->persist($accountReport);
            $createdReports[] = $accountReport;
            unset($accountReport);
        }

        $this->flushThenDetach($createdReports);
        unset($createdReports);
    }

    /**
     * @param AdNetworkInterface[] $demandPartners
     */
    protected function createDemandPartnerReports(array $demandPartners)
    {
        foreach($demandPartners as $adNetwork) {
            if (!$adNetwork instanceof VideoDemandPartnerInterface) {
                continue;
            }

            $this->logger->info(sprintf('creating report for ad network %d', $adNetwork->getId()));

            /** @var DemandPartnerReportInterface $demandPartnerReport */
            $demandPartnerReport = $this->reportCreator->getReport(
               new DemandPartnerReportType($adNetwork)
            );

            $this->logger->info('Persisting report for ad network');
            $this->om->persist($demandPartnerReport);
            $this->logger->info(sprintf('Finished report for ad network %d', $adNetwork->getId()));

            unset($demandPartnerReport);
        }
    }

    protected function flushThenDetach($entities)
    {
        $this->om->flush();
        $this->detach($entities);
    }

    protected function detach($entities)
    {
        $myEntities = is_array($entities) ? $entities : [$entities];
        foreach ($myEntities as $entity) {
            $this->om->detach($entity);
        }
    }

    /**
     * @return DateTime
     */
    public function getReportDate()
    {
        return $this->reportCreator->getDate();
    }

    /**
     * @param DateTime $reportDate
     * @return $this
     */
    public function setReportDate(DateTime $reportDate)
    {
        $this->reportCreator->setDate($reportDate);

        return $this;
    }
}