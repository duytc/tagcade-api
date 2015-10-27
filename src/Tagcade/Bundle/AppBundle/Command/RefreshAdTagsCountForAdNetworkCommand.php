<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * Provides a command-line interface for generating and assigning uuid for all publisher
 */
class RefreshAdTagsCountForAdNetworkCommand extends ContainerAwareCommand
{

    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:ad-network:refresh-ad-tag-count')
            ->setDescription('Recalculate active and paused ad tags for all ad network');
        ;
    }

    /**
     * Execute the CLI task
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = 0;
        $adNetworkManager = $this->getContainer()->get('tagcade.domain_manager.ad_network');
        $allAdNetworks = $adNetworkManager->all();
        /**
         * @var AdNetworkInterface $adNetwork
         */
        foreach($allAdNetworks as $adNetwork) {
            $adTags = $adNetwork->getAdTags();
            $activeCount = count(array_filter($adTags, function(AdTagInterface $adTag) {
                return $adTag->isActive();
            }));

            $pausedCount = count(array_filter($adTags, function(AdTagInterface $adTag) {
                return !$adTag->isActive();
            }));

            $adNetwork->setActiveAdTagsCount($activeCount);
            $adNetwork->setPausedAdTagsCount($pausedCount);
            $adNetworkManager->save($adNetwork);
            
            $count++;
        }

        $output->writeln(sprintf('%d AdNetwork(s) get updated !', $count));
    }
}
