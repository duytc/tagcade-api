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
                InputArgument::REQUIRED,
                'Id of publisher to be updated'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publisherId   = $input->getArgument('id');

        if (null === $publisherId) {
            throw new InvalidArgumentException('publisher id required');
        }

        $userManager = $this->getContainer()->get('tagcade_user.domain_manager.user');
        $publisher = $userManager->findPublisher($publisherId);

        if ($publisher === false) {
            throw new RuntimeException('that publisher is not existed');
        }

        $output->writeln('start updating billed amount for publisher');

        $billingEditor = $this->getContainer()->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');
        $billingEditor->updateBilledAmountToCurrentDateForPublisher($publisher);

        $output->writeln('finish updating billed amount for the publisher');
    }
}