<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;


use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\HistoryReportCreator;

class CreatePerformanceReportForPartnerCommand extends ContainerAwareCommand
{
    const SUCCESS = 0;
    const FAILURE = 1;

    protected function configure()
    {
        $this
            ->setName('tc:report:create-partner-report')
            ->addOption('publisher', 'p', InputOption::VALUE_OPTIONAL, 'Publisher id to be create performance partner report. Default is all publishers')
            ->addOption('start-date', 'f', InputOption::VALUE_OPTIONAL, 'The start date of report, format as YYYY-MM-DD. Default is yesterday')
            ->addOption('end-date', 't', InputOption::VALUE_OPTIONAL, 'The end date of report, format as YYYY-MM-DD. End date equals to start date if not set')
            ->addOption('override', 'override', InputOption::VALUE_NONE, 'replace the existing reports with the new data')
            ->setDescription('Create performance report for partners and sub publishers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerInterface $container */
        $container = $this->getContainer();

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');

        /** @var PublisherManagerInterface $publisherManager */
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');

        /* 1. get input arguments */
        $publisherId = $input->getOption('publisher');
        $publishers = [];
        if ($publisherId != null) {
            $publisher = $publisherManager->findPublisher($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                $logger->error(sprintf('Publisher Id %d is not valid', $publisherId));
                return self::FAILURE;
            }

            $publishers[] = $publisher;
        }
        else {
            $publishers = $publisherManager->allActivePublishers();
        }

        $startDateStr = $input->getOption('start-date');
        $endDateStr = $input->getOption('end-date');

        $override = filter_var($input->getOption('override'), FILTER_VALIDATE_BOOLEAN);

        $startDate = $startDateStr == null ? new \DateTime('yesterday') : \DateTime::createFromFormat('Y-m-d', $startDateStr);
        $endDate = $endDateStr != null ? \DateTime::createFromFormat('Y-m-d', $endDateStr) : (clone $startDate);

        if (!$startDate instanceof \DateTime || !$endDate instanceof \DateTime) {
            throw new \Exception(sprintf('Invalid date time format for either start date %s or end date %s', $startDateStr, $endDateStr));
        }

        if ($startDate > $endDate) {
            $logger->error('start date must be before end date');
            return self::FAILURE;
        }

        /** @var HistoryReportCreator $historyReportCreator */
        $historyReportCreator = $container->get('tagcade.service.report.performance_report.display.creator.history_report_creator');

        $endDate = $endDate->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval, $endDate);

        foreach ($dateRange as $date) {
            /**
             * @var \DateTime $date
             */
            $logger->info(sprintf("%s processing...", $date->format('Y-m-d')));

            try {
                $historyReportCreator
                    ->setReportDate($date)
                    ->createAndSave($publishers, $override);

                $logger->info(sprintf("%s created", $date->format('Y-m-d')));
                gc_collect_cycles();
            } catch(UniqueConstraintViolationException $ex) {
                $output->writeln(sprintf('<error>One of the reports might have been created before. Try "--override" option instead</error>'));
                return self::FAILURE;
            }
        }

        $logger->info('Performance report for partners and sub publishers done');

        return self::SUCCESS;
    }
} 