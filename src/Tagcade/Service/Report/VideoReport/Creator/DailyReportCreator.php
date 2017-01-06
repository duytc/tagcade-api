<?php

namespace Tagcade\Service\Report\VideoReport\Creator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Entity\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReport;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\PlatformReport;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as DemandPartnerReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as AccountReportType;
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

    /**
     * @var PublisherManagerInterface
     */
    private $publisherManager;

    public function __construct(ObjectManager $om, ReportCreatorInterface $reportCreator, PublisherManagerInterface $publisherManager)
    {
        $this->om = $om;
        $this->reportCreator = $reportCreator;
        $this->publisherManager = $publisherManager;
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
     * @param $override
     */
    public function createAndSave(array $publishers, $videoDemandPartners, $override = false)
    {
        $platformReportRepository = $this->om->getRepository(PlatFormReport::class);
        $accountReportRepository = $this->om->getRepository(AccountReport::class);
        $report = current($platformReportRepository->getReportsFor($this->reportCreator->getDate(), $this->reportCreator->getDate()));
        if ($report instanceof ReportInterface && $override === false) {
            throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
        }

        $this->createAccountReports($publishers, $override);
        $report = new PlatFormReport();
        $report->setDate($this->reportCreator->getDate());

        $accountReports = $accountReportRepository->getReportsByDateRange($report->getDate(), $report->getDate());

        /** @var AccountReport $accountReport */
        foreach($accountReports as $accountReport) {
            $accountReport->setSuperReport($report);
            $report->addSubReport($accountReport);
        }

        $report->setCalculatedFields();

        if ($override === true && $report instanceof ReportInterface) {
            $platformReportRepository->overrideReport($report);
            foreach ($accountReports as $accountReport) {
                $this->om->detach($accountReport);
            }
            unset($accountReports);
            $this->om->detach($report);
            unset($report);
            return;
        }

        $report = current($platformReportRepository->getReportsFor($this->reportCreator->getDate(), $this->reportCreator->getDate()));
        if ($override === false && $report instanceof ReportInterface) {
            throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
        }

        if ($override === true && $report instanceof ReportInterface) {
            $this->om->remove($report);
            $this->om->flush();
        }

        $this->logger->info('Persisting platform report');
        $this->om->persist($report);

        $this->logger->info('flushing then detaching platform report');
        $this->om->flush();

        foreach ($accountReports as $accountReport) {
            $this->om->detach($accountReport);
        }
        unset($accountReports);
        $this->om->detach($report);

        unset($accountReports);
        unset($report);

        $this->logger->info('Finished platform report');
        gc_collect_cycles();

        $this->createDemandPartnerReports($videoDemandPartners, $override);

        $this->om->flush();
        unset($adNetworks);

        $this->logger->info('Finished all network reports');

        gc_collect_cycles();
    }

    public function createPlatformReport(DateTime $reportDate, $override = false)
    {
        $platformReportRepository = $this->om->getRepository(PlatformReport::class);
        $platformReport = current($platformReportRepository->getReportsFor($reportDate, $reportDate));
        if ($platformReport instanceof ReportInterface && $override === false) {
            throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
        }

        $report = new PlatformReport();
        $report->setDate($reportDate);

        $accountReportRepository = $this->om->getRepository(AccountReport::class);
        $allPublishers = $this->publisherManager->allPublisherWithVideoModule();

        $allPublishers = array_map(function(PublisherInterface $publisher) {
            return $publisher->getId();
        }, $allPublishers);

        $platformCounts = $accountReportRepository->getAggregatedReportsByDateRange($allPublishers, $reportDate, $reportDate);


        $accountReports = $accountReportRepository->getReportsByDateRange($reportDate, $reportDate);
        foreach ($accountReports as $accountReport) {
            $accountReport->setSuperReport($report);
            $report->addSubReport($accountReport);
        }

        $report->parseData($platformCounts)
            ->setThresholdBilledAmount($chainToSubReports = false); // we don't need to calculate for sub reports

        $report->setCalculatedFields($chainToSubReports = false);

        if ($override === true && $platformReport instanceof ReportInterface) {
            $platformReportRepository->overrideReport($report);
            foreach ($accountReports as $accountReport) {
                $this->om->detach($accountReport);
            }
            unset($accountReports);
            $this->om->detach($report);
            unset($report);
            return;
        }

        $this->om->persist($report);
        $this->om->flush();
    }

    protected function createAccountReports(array $publishers, $override = false)
    {
        $accountReportRepository = $this->om->getRepository(AccountReport::class);
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $accountReport = $this->reportCreator->getReport(
                new AccountReportType($publisher)
            );

            $report = current($accountReportRepository->getReportsFor($publisher, $this->reportCreator->getDate(), $this->reportCreator->getDate()));
            if ($report instanceof ReportInterface && $override === false) {
                throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
            }

            if ($report instanceof ReportInterface && $override === true) {
                $this->om->remove($report);
                $this->om->flush();
            }

            /**
             * @var AccountReport $accountReport
             */
            $this->om->persist($accountReport);

            $this->om->flush();
            $this->om->detach($accountReport);
            gc_collect_cycles();
            unset($accountReport);
        }
    }

    /**
     * @param AdNetworkInterface[] $demandPartners
     * @param $override = false
     */
    protected function createDemandPartnerReports(array $demandPartners, $override = false)
    {
        $demandPartnerReportRepository = $this->om->getRepository(DemandPartnerReport::class);
        foreach($demandPartners as $demandPartner) {
            if (!$demandPartner instanceof VideoDemandPartnerInterface) {
                continue;
            }

            $this->logger->info(sprintf('creating report for video demand partner %d', $demandPartner->getId()));

            /** @var DemandPartnerReportInterface $demandPartnerReport */
            $demandPartnerReport = $this->reportCreator->getReport(
               new DemandPartnerReportType($demandPartner)
            );

            $report = current($demandPartnerReportRepository->getReportsFor($demandPartner, $this->reportCreator->getDate(), $this->reportCreator->getDate()));
            if ($override === false && $report instanceof ReportInterface) {
                throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
            }

            if ($override === true && $report instanceof ReportInterface) {
                $this->om->remove($report);
                $this->om->flush();
            }

            $this->logger->info('Persisting report for ad network');
            $this->om->persist($demandPartnerReport);
            $this->logger->info(sprintf('Finished report for video demand partner %d', $demandPartner->getId()));

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