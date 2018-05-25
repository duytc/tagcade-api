<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
            ->setDescription('Recalculate active and paused ad tags for ad networks')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'The ad network id to be updated'
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'If set, all ad networks will be updated'
            )
            ->addOption(
                'publisher',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Publisher id, if set, all ad networks belongs to this publisher will be updated'
            )
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
        $publisherId = $input->getOption('publisher');
        $adNetworkManager = $this->getContainer()->get('tagcade.domain_manager.ad_network');
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        if ($input->getOption('all')) {
            $allAdNetworks = $adNetworkManager->allActiveAdNetworks();
            $progress = new ProgressBar($output, count($allAdNetworks));
            /** @var AdNetworkInterface $adNetwork */
            foreach ($allAdNetworks as $adNetwork) {
                $this->recalculateAdTagCountForAdNetwork($adNetwork);
                $count++;
                $progress->advance();
            }

            $progress->finish();
            $output->writeln('');
            $output->writeln(sprintf('%d ad network(s) have been updated', $count));
        } else if($publisherId) {
            $publisherId = filter_var($publisherId, FILTER_VALIDATE_INT);
            $publisher = $publisherManager->find($publisherId);
            if (!$publisher instanceof PublisherInterface) {
                $output->writeln(sprintf('<error>The Publisher %d does not exist</error>', $publisherId));
                return;
            }

            if (!$publisher->isEnabled()) {
                $output->writeln(sprintf('0 ad network have been updated, publisher is inactive', $count));
                return;
            }
            
            $allAdNetworks = $adNetworkManager->getAdNetworksForPublisher($publisher);
            $progress = new ProgressBar($output, count($allAdNetworks));
            /** @var AdNetworkInterface $adNetwork */
            foreach ($allAdNetworks as $adNetwork) {
                $this->recalculateAdTagCountForAdNetwork($adNetwork);
                $count++;
                $progress->advance();
            }

            $progress->finish();
            $output->writeln('');
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
        $adTags = $adTags instanceof Collection ? $adTags->toArray() : $adTags;

        $activeCount = count(array_filter($adTags, function ($adTag) {
            return $adTag instanceof AdTagInterface  && $adTag->isActive();
        }));

        $pausedAndAutoPausedCount = count(array_filter($adTags, function ($adTag) {
            return $adTag instanceof AdTagInterface  && !$adTag->isActive();
        }));

        $adNetwork->setActiveAdTagsCount($activeCount);
        $adNetwork->setPausedAdTagsCount($pausedAndAutoPausedCount);
        $adNetworkManager->save($adNetwork);
    }
}
