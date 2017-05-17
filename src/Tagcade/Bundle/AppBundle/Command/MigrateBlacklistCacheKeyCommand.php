<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;

class MigrateBlacklistCacheKeyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:blacklist-keys:migration')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'whether to delete old keys')
            ->addOption('oldBlacklistPrefix', 'obp', InputOption::VALUE_OPTIONAL, 'old blacklist prefix, eg "display:domain_blacklist".')
            ->setDescription('refresh cache keys for blacklist, allow user to delete old keys');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // migrate blacklist keys
        $refreshDisplayBlacklistService = $this->getContainer()->get('tagcade_app.service.core.display_blacklist.refresh_display_blacklist_cache');
        $displayBlacklistManager = $this->getContainer()->get('tagcade.domain_manager.display.blacklist');
        $displayBlacklistCacheManager = $this->getContainer()->get('tagcade.cache.display_blacklist_cache_manager');

        $refreshDisplayBlacklistService->refreshCacheForAllBlacklist();
        $output->writeln('All blacklist have been migrated!');

        //remove old keys
        $delete = $input->getOption('delete');
        $oldPrefix = $input->getOption('oldBlacklistPrefix');
        if ($delete && !empty($oldPrefix)) {
            $output->writeln('Deleting old keys...');

            $redis = $displayBlacklistCacheManager->getRedis();
            $blacklists = $displayBlacklistManager->all();

            /** @var DisplayBlacklistInterface $blacklist */
            foreach ($blacklists as $blacklist) {
                $redis->delete(sprintf('%s:%d', $oldPrefix, $blacklist->getId()));
            }
        }

        $output->writeln('Done!');
    }
}