<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReport;
use Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform\PlatformReport;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
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
     * @param $override = false
     */
    public function createAndSave(array $publishers, $override = false)
    {
        $platformReportRepository = $this->om->getRepository(PlatFormReport::class);
        $accountReportRepository = $this->om->getRepository(AccountReport::class);
        $report = current($platformReportRepository->getReportFor($this->reportCreator->getDate(), $this->reportCreator->getDate()));
        if ($report instanceof ReportInterface && $override === false) {
            throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
        }

        $this->createReportsForPublishers($publishers, $override);
        $report = new PlatFormReport();
        $report->setDate($this->reportCreator->getDate());


        $accountReports = $accountReportRepository->getReportsByDateRange($report->getDate(), $report->getDate());
        $requests = 0;
        $billedAmount = 0;
        /** @var AccountReport $accountReport */
        foreach($accountReports as $accountReport) {
            $accountReport->setSuperReport($report);
            $report->addSubReport($accountReport);
            $requests += $accountReport->getRequests();
            $billedAmount += $accountReport->getBilledAmount();
        }

        $report->setRequests($requests)
            ->setBilledAmount($billedAmount)
            ->setCalculatedFields();

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
    }

    public function createReportsForPublishers(array $publishers, $override = false)
    {
        $accountReportRepository = $this->om->getRepository(AccountReport::class);

        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $this->logger->info(sprintf('start creating header bidding report for publisher %s', $publisher->getUser()->getUsername()));
            $accountReport = $this->reportCreator->getReport(
                new AccountReportType($publisher)
            );

            $report = current($accountReportRepository->getReportFor($publisher, $this->reportCreator->getDate(), $this->reportCreator->getDate()));
            if ($report instanceof ReportInterface && $override === false) {
                throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
            }

            if ($report instanceof ReportInterface && $override === true) {
                $this->om->remove($report);
                $this->om->flush();
            }

            /** @var AccountReport $accountReport */
            $this->om->persist($accountReport);

            $this->om->flush();
            $this->om->detach($accountReport);
            gc_collect_cycles();

            unset($accountReport);
            $this->logger->info(sprintf('finish creating header bidding report for publisher %s', $publisher->getUser()->getUsername()));
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