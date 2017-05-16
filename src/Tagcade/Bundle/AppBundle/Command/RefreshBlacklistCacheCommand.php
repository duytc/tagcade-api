<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;

class RefreshBlacklistCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:blacklist-cache:refresh')
            ->addOption('id', 'id', InputOption::VALUE_REQUIRED, 'id of the entity to be refresh')
            ->setDescription('refresh cache for the given blacklist, removing duplicates included. Default to refresh all');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getOption('id');
        $blackListManager = $this->getContainer()->get('tagcade.domain_manager.display.blacklist');
        $refreshBlacklistService = $this->getContainer()->get('tagcade_app.service.core.display_blacklist.refresh_display_blacklist_cache');
        if ($id) {
            $blackList = $blackListManager->find($id);
            if (!$blackList instanceof DisplayBlacklistInterface) {
                $output->writeln(sprintf('<error>Blacklist %d does not exists!</error>', $id));
                return;
            }

            $refreshBlacklistService->refreshCacheForSingleBlacklist($blackList);
            $output->writeln('The blacklist\'s cache has been refreshed!');

            return;
        }

        $blackLists = $blackListManager->all();
        $totalBlacklist = count($blackLists);
        $progress = new ProgressBar($output, $totalBlacklist);
        $progress->start();
        foreach ($blackLists as $blackList) {
            $refreshBlacklistService->refreshCacheForSingleBlacklist($blackList);
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln(sprintf('%d blacklists get refreshed', $totalBlacklist));
    }
}