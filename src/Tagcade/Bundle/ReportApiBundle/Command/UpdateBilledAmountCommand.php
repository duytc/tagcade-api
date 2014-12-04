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
                'Id of publisher to be updated'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publisherId   = $input->getArgument('id');
        $userManager = $this->getContainer()->get('tagcade_user.domain_manager.user');
        $billingEditor = $this->getContainer()->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');

        $output->writeln('start updating billed amount for publisher');

        if (null === $publisherId) {
            $updatedCount = $billingEditor->updateBilledAmountToCurrentDateForAllPublishers();
        }
        else {
            $publisher = $userManager->findPublisher($publisherId);
            $updatedCount = $billingEditor->updateBilledAmountToCurrentDateForPublisher($publisher);
        }
        
        $output->writeln( sprintf('finish updating billed amount. Total %d publisher(s) gets updated.', $updatedCount));
    }
}