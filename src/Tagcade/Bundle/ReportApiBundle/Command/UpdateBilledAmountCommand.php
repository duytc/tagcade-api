<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;

class UpdateBilledAmountCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:billing:update')
            ->setDescription('Update billed amount corresponding to total slot opportunities up to current day and pre-configured thresholds')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'Id of publisher to be updated. Otherwise all publishers get updated'
            )
            ->addArgument(
                'month',
                InputArgument::OPTIONAL,
                'Month that the billed amount needs to be recalculated. Default is current month'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publisherId   = $input->getArgument('id');
        $month = $input->getArgument('month');

        if (null === $month) {
            $month = new DateTime('yesterday');
        }

        $userManager = $this->getContainer()->get('tagcade_user.domain_manager.user');

        $output->writeln('start updating billed amount for publisher');

        $billingEditor = $this->getContainer()->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');

        if (null === $publisherId) {
            $updatedCount = $billingEditor->updateBilledAmountToCurrentDateForAllPublishers();
        }
        else {

            $publisher = $userManager->findPublisher($publisherId);

            if ($publisher === false) {
                throw new RuntimeException('that publisher is not existed');
            }

            $updatedCount = $billingEditor->updateBilledAmountToCurrentDateForPublisher($publisher, $month);
        }
        
        $output->writeln( sprintf('finish updating billed amount. Total %d publisher(s) gets updated.', $updatedCount));
    }
}