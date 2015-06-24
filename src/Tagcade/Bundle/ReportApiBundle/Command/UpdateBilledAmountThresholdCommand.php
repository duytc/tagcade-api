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
use Tagcade\Model\User\Role\PublisherInterface;

class UpdateBilledAmountThresholdCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:billing:update-threshold')
            ->setDescription('Update billed amount corresponding to total slot opportunities up to current day and pre-configured thresholds')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_OPTIONAL,
                'Id of publisher to be updated. Otherwise all publishers get updated'
            )
            ->addOption(
                'month',
                null,
                InputOption::VALUE_OPTIONAL,
                'Month(YYYY-MM) that the billed amount needs to be recalculated. Default is current month'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publisherId   = $input->getOption('id');
        $month = $input->getOption('month');

        $month = null === $month ? new DateTime('yesterday') : DateTime::createFromFormat('Y-m', $month);

        if (false === $month ) {
            throw new InvalidArgumentException('Invalid month input');
        }

        $output->writeln('start updating billed amount for publisher');

        $userManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $billingEditor = $this->getContainer()->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');

        if (null === $publisherId) {
            $updatedCount = $billingEditor->updateBilledAmountThresholdForAllPublishers($month);
        }
        else {

            $publisher = $userManager->findPublisher($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                throw new RuntimeException('that publisher is not existed');
            }

            $updatedCount = $billingEditor->updateBilledAmountThresholdForPublisher($publisher, $month);
        }
        
        $output->writeln( sprintf('finish updating billed amount. Total %d publisher(s) gets updated.', $updatedCount));
    }
}