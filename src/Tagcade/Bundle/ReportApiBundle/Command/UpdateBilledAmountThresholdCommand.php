<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
                'i',
                InputOption::VALUE_OPTIONAL,
                'Id of publisher to be updated. Otherwise all publishers get updated'
            )
            ->addOption(
                'month',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Month(YYYY-MM) that the billed amount needs to be recalculated. Default is current month'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        // run the command with -vv verbosity to show messages
        // https://symfony.com/doc/current/cookbook/logging/monolog_console.html
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');

        $publisherId   = $input->getOption('id');
        $month = $input->getOption('month');

        $yesterday = new DateTime('yesterday');
        $month = null === $month ? $yesterday : DateTime::createFromFormat('Y-m-d', sprintf('%s-%s', $month, $yesterday->format('d')));

        if (false === $month ) {
            throw new InvalidArgumentException('Invalid month input');
        }

        $userManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $billingEditor = $this->getContainer()->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');
        $billingEditor->setLogger($logger);

        if (null === $publisherId) {
            $logger->info('start updating billed amount for all publishers');

            $billingEditor->updateBilledAmountThresholdForAllPublishers($month);

            $logger->info('finish updating billed amount for all publishers');
        }
        else {
            $logger->info('start updating billed amount for publisher');

            $publisher = $userManager->findPublisher($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                throw new RuntimeException('that publisher is not valid');
            }

            $billingEditor->updateBilledAmountThresholdForPublisher($publisher, $month);

            $logger->info('finish updating billed amount');
        }
    }
}