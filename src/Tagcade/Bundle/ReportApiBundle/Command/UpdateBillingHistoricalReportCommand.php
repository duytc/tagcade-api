<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;

class UpdateBillingHistoricalReportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:billing:update-historical')
            ->setDescription('Update billed amount regarding to new input threshold')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Id of publisher to be updated. Otherwise all publishers get updated'
            )
            ->addOption(
                'rate',
                null,
                InputOption::VALUE_REQUIRED,
                'Cpm rate that newly defines for this publisher'
            )
            ->addOption(
                'startDate',
                null,
                InputOption::VALUE_REQUIRED,
                'start date (YYYY-MM-DD) that billed amount will be updated with new cpm rate'
            )
            ->addOption(
                'endDate',
                null,
                InputOption::VALUE_OPTIONAL,
                'end date (YYYY-MM-DD) that billed amount will be updated with new cpm rate. If not specified, the date is et to yesterday.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publisherId   = $input->getArgument('id');
        $cpmRate = $input->getOption('rate');
        $startDate = $input->getOption('startDate');
        $endDate = $input->getOption('endDate');

        $startDate = DateTime::createFromFormat('Y-m-d', $startDate);
        $endDate = null === $endDate ? new DateTime('yesterday') : DateTime::createFromFormat('Y-m-d', $endDate);

        if (false === $startDate || false === $endDate || $startDate > $endDate) {
            throw new InvalidArgumentException('Date range is not valid');
        }

        if (!is_numeric($cpmRate) || $cpmRate < 0) {
            throw new InvalidArgumentException('Cpm rate must be numeric and non-negative');
        }

        $userManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $publisher = $userManager->findPublisher($publisherId);

        if ($publisher === false) {
            throw new RuntimeException('that publisher is not existed');
        }

        $output->writeln('start updating billing historical report');

        $billingEditor = $this->getContainer()->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');
        $billingEditor->updateHistoricalBilledAmount($publisher, (float)$cpmRate, $startDate, $endDate);

        $output->writeln('finish updating billing historical report');
    }
} 