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
        $this->logger->info('Getting platform report');
        $platformReport = $this->reportCreator->getReport(
            new PlatformReportType($publishers)
        );

        $report = current($platformReportRepository->getReportFor($this->reportCreator->getDate(), $this->reportCreator->getDate()));
        if ($report instanceof ReportInterface && $override === false) {
            throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
        }

        if ($report instanceof ReportInterface && $override === true) {
            $this->om->remove($report);
            $this->om->flush();
        }

        $this->logger->info('Persisting platform report');
        $this->om->persist($platformReport);

        $this->logger->info('flushing then detaching platform report');
        $this->om->flush();

        unset($platformReport);
        $this->logger->info('Finished platform report');
        gc_collect_cycles();
    }

    public function createReportsForPublishers(array $publishers)
    {
        $this->createAccountReports($publishers);

        $this->om->flush();
    }

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