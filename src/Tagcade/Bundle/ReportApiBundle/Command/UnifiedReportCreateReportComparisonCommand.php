<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\UnifiedReport\ReportComparisonCreatorInterface;

/**
 * Provides a command-line interface for renewing cache using cli
 */
class UnifiedReportCreateReportComparisonCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:unified-report:compare')
            ->addOption('publisher', 'p', InputOption::VALUE_REQUIRED, 'Publisher id')
            ->addOption('start-date', 'f', InputOption::VALUE_REQUIRED, 'Start date (YYYY-MM-DD) of the report. ')
            ->addOption('end-date', 't', InputOption::VALUE_REQUIRED, 'End date of the report (YYYY-MM-DD). Default is yesterday', (new \DateTime('yesterday'))->format('Y-m-d'))
            ->addOption('override', 'override', InputOption::VALUE_NONE, 'allow override existing data in case of duplicated unique key')
            ->setDescription('Create report comparison between tagcade and unified report ');;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var ReportComparisonCreatorInterface $reportComparisonCreator
         */
        $reportComparisonCreator = $this->getContainer()->get('tagcade.service.report.unified_report.report_comparison_creator');

        $startDateStr = $input->getOption('start-date');
        $endDateStr = $input->getOption('end-date');

        $startDate = $startDateStr == null ? new \DateTime('yesterday') : \DateTime::createFromFormat('Y-m-d', $startDateStr);
        $endDate = $endDateStr != null ? \DateTime::createFromFormat('Y-m-d', $endDateStr) : (clone $startDate);

        if (!$startDate instanceof \DateTime || !$endDate instanceof \DateTime) {
            throw new \Exception(sprintf('Invalid date time format for either start date %s or end date %s', $startDateStr, $endDateStr));
        }

        $override = filter_var($input->getOption('override'), FILTER_VALIDATE_BOOLEAN);

        $today = new \DateTime('today');

        if ($endDate >= $today) {
            $output->writeln('<warning>The end date is greater or equal than today, the tool might not work properly!</warning>');
            $endDate = new \DateTime('yesterday');
        }
        
        if ($startDate > $endDate) {
            throw new \Exception('startDate must be less than or equal to endDate');
        }

        $publisherId = $input->getOption('publisher');
        if (!is_numeric($publisherId) || (int)$publisherId < 1) {
            throw new \Exception(sprintf('Expect positive integer publisher id. The value %s is entered', $publisherId));
        }

        $userManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $publisher = $userManager->findPublisher($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            throw new \Exception(sprintf('Not found publisher with id %d', $publisherId));
        }

        try {
            $reportComparisonCreator->updateComparisonForPublisher($publisher, $startDate, $endDate, $override);
        } catch (UniqueConstraintViolationException $ex) {
            $output->writeln(sprintf('<error>Got error: %s</error>', $ex->getMessage()));
            $output->writeln(sprintf('<error>Some data might have been created before. Use option "--override" instead</error>'));
        }
    }
}
