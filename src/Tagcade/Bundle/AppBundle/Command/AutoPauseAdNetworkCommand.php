<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;

class AutoPauseAdNetworkCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tc:ad-network:do-auto-pause')
            ->addOption('adNetwork', 'd', InputOption::VALUE_OPTIONAL, 'ad network id to validate and do auto pause.')
            ->setDescription('Do pause for ad networks that have reached impression cap and network opportunity cap per day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adNetwork = $input->getOption('adNetwork');
        $container = $this->getContainer();
        $logger = $container->get('logger');
        $adNetworkManager = $container->get('tagcade.domain_manager.ad_network');
        $adTagManager = $container->get('tagcade.domain_manager.ad_tag');
        $eventCounter = $container->get('tagcade.service.report.performance_report.display.counter.cache_event_counter');
        $autoPauseForAdNetworks = [];

        if ($adNetwork != null) {
            $adNetwork = $adNetworkManager->find($adNetwork);
            if (!$adNetwork instanceof AdNetworkInterface) {
                throw new \Exception(sprintf('Not found that ad network %s', $adNetwork));
            }

            $autoPauseForAdNetworks[] = $adNetwork;
        }

        if (count($autoPauseForAdNetworks) < 1) {
            $autoPauseForAdNetworks = $adNetworkManager->allHasCap();
        }

        $pausedNetworkCount = 0;
        foreach ($autoPauseForAdNetworks as $nw) {
            /**
             * @var AdNetworkInterface $nw
             */
            $adTags = $adTagManager->getAdTagsForAdNetwork($nw);
            if (count($adTags) < 1) {
                continue;
            }

            $opportunityCap = $nw->getNetworkOpportunityCap();
            $impressionCap = $nw->getImpressionCap();

            if (($opportunityCap == null || $opportunityCap < 1) && ($impressionCap == null || $impressionCap < 1)) {
                continue; // ignore networks that do not set both impression cap and opportunity cap
            }

            $logger->info(sprintf('Checking impression cap and network opportunity cap for ad network %d', $nw->getId()));

            $opportunityCount = 0;
            $impressionCount = 0;
            foreach ($adTags as $adTag) {
                /**
                 * @var AdTagInterface $adTag
                 */
                $opportunityCount += $eventCounter->getOpportunityCount($adTag->getId());
                $impressionCount +=  $eventCounter->getImpressionCount($adTag->getId());
            }

            if (($opportunityCap > 0 && $opportunityCap <= $opportunityCount) || ($impressionCap > 0 && $impressionCap <= $impressionCount)) {
                $logger->info(sprintf('Ad network %d will be PAUSED shortly', $nw->getId()));
                $cmd = sprintf('%s tc:ad-tag-status:update %d --status=%d', $this->getAppConsoleCommand(), $adNetwork->getId(), AdTagInterface::AUTO_PAUSED);
                $this->executeProcess($process = new Process($cmd), ['timeout' => 200], $logger);

                $pausedNetworkCount ++;
            }
            else {
                $logger->info(sprintf('Network %d is still RUNNING with (network opportunity cap %d, current opportunities %d) and (network impression cap %d, current impressions %d)', $nw->getId(), $opportunityCap, $opportunityCount, $impressionCap, $impressionCount));
            }
        }

        $logger->info(sprintf('There are %d ad networks get paused', $pausedNetworkCount));
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

        $process->mustRun(function($type, $buffer) use($logger) {
            if (Process::ERR === $type) {
                $logger->error($buffer);
            } else {
                $logger->info($buffer);
            }
        }
        );
    }
} 