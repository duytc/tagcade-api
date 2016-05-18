<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a command-line interface for renewing cache using cli
 */
class RefreshAdSlotCacheCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:cache:refresh-adslots')
            ->setDescription('Create initial ad slot cache if needed to avoid slams');;
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
        $tagCacheManager = $this->getContainer()->get('tagcade.cache.tag_cache_manager');

        $tagCacheManager->refreshCache();

        $output->writeln('Ad slot cache is now refreshed');
    }
}
