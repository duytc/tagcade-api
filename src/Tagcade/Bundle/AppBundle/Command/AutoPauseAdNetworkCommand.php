<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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

            $output->writeln(sprintf('Checking impression cap and network opportunity cap for ad network %d', $nw->getId()));

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
                $output->writeln(sprintf('Ad network %d will be PAUSED shortly', $nw->getId()));
                $adTagManager->updateAdTagStatusForAdNetwork($nw, $active = AdTagInterface::AUTO_PAUSED);
                $pausedNetworkCount ++;
            }
            else {
                $output->writeln(sprintf('Network %d is still RUNNING with (network opportunity cap %d, current opportunities %d) and (network impression cap %d, current impressions %d)', $nw->getId(), $opportunityCap, $opportunityCount, $impressionCap, $impressionCount));
            }
        }

        $output->writeln(sprintf('There are %d ad networks get paused', $pausedNetworkCount));
    }
} 