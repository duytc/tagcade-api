<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;

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
            ->setDescription('Recalculate active and paused ad tags for ad networks')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'The ad network id to be updated'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, all ad networks will be updated'
            );
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
        if ($input->getOption('all')) {
            $allAdNetworks = $adNetworkManager->all();
            /** @var AdNetworkInterface $adNetwork */
            foreach ($allAdNetworks as $adNetwork) {
                $this->recalculateAdTagCountForAdNetwork($adNetwork);
                $count++;
            }

            $output->writeln(sprintf('%d ad network(s) have been updated', $count));
        } else {
            $id = $input->getArgument('id');

            if ($id) {
                $adNetwork = $adNetworkManager->find(filter_var($id, FILTER_VALIDATE_INT));

                if ($adNetwork instanceof AdNetworkInterface) {
                    $this->recalculateAdTagCountForAdNetwork($adNetwork);
                    $count++;
                    $output->writeln(sprintf('%d Ad Network(s) have been updated', $count));
                } else {
                    $output->writeln('<error>The AdNetwork does not exist</error>');
                }
            } else {
                $output->writeln('<question>Are you missing {id} or --all option ?"</question>');
                $output->writeln('<question>Try "php app/console tc:ad-network:refresh-ad-tag-count {id}"</question>');
                $output->writeln('<question>Or "php app/console tc:ad-network:refresh-ad-tag-count --all"</question>');
            }
        }
    }

    private function recalculateAdTagCountForAdNetwork(AdNetworkInterface $adNetwork)
    {
        $adNetworkManager = $this->getContainer()->get('tagcade.domain_manager.ad_network');
        $adTags = $adNetwork->getAdTags();
        $activeCount = count(array_filter($adTags, function (AdTagInterface $adTag) {
            return $adTag->isActive();
        }));

        $pausedCount = count(array_filter($adTags, function (AdTagInterface $adTag) {
            return !$adTag->isActive();
        }));

        $adNetwork->setActiveAdTagsCount($activeCount);
        $adNetwork->setPausedAdTagsCount($pausedCount);
        $adNetworkManager->save($adNetwork);
    }
}
