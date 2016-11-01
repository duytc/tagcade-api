<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\User\Role\PublisherInterface;

class UpdateBilledAmountVideoThresholdCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:billing:update-video-threshold')
            ->setDescription('Update billed amount corresponding to total video impressions up to current day and pre-configured thresholds')
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
        $billingEditor = $this->getContainer()->get('tagcade.service.report.video_report.billing.billed_amount_editor');
        $billingEditor->setLogger($logger);

        if (null === $publisherId) {
            $publishers = $userManager->allPublisherWithVideoModule();

            /**
             * @var PublisherInterface $publisher
             */
            foreach($publishers as $publisher) {
                $id = $publisher->getId();
                $logger->info(sprintf('Start updating threshold billed amount for publisher %d', $id));
                $cmd = sprintf('%s tc:billing:update-video-threshold --id %d --month %s', $this->getAppConsoleCommand(), $id, $month->format('Y-m-d'));
                $this->executeProcess($process = new Process($cmd), [], $logger);
            }
        }
        else {
            $logger->info('start updating billed amount for publisher');

            $publisher = $userManager->findPublisher($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                throw new RuntimeException('that publisher is not valid');
            }

            $billingEditor->updateVideoBilledAmountThresholdForPublisher($publisher, $month);

            $logger->info('finish updating billed amount');
        }
    }

    protected function getAppConsoleCommand()
    {
        $pathToSymfonyConsole = $this->getContainer()->getParameter('kernel.root_dir');
        $environment = $this->getContainer()->getParameter('kernel.environment');
        $debug = $this->getContainer()->getParameter('kernel.debug');

        $command = sprintf('php %s/console --env=%s', $pathToSymfonyConsole, $environment);

        if (!$debug) {
            $command .= ' --no-debug';
        }

        return $command;
    }

    protected function executeProcess(Process $process, array $options, LoggerInterface $logger)
    {
        if (array_key_exists('timeout', $options)) {
            $process->setTimeout($options['timeout']);
        }

        try {
            $process->mustRun(function($type, $buffer) use($logger) {
                if (Process::ERR === $type) {
                    $logger->error($buffer);
                } else {
                    $logger->info($buffer);
                }
            }
            );
        } catch (ProcessFailedException $ex) {
            throw $ex;
        }
    }
}