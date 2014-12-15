<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Provides a command-line interface for renewing cache using cli
 */
class RefreshCacheCommand extends ContainerAwareCommand
{

    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:cache:refresh-all')
            ->setDescription('Create initial ad slot cache if needed to avoid slams');
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
        $tagCache = $this->getContainer()->get('tagcade.legacy.tag_cache');

        $tagCache->refreshCache();

        $output->writeln('Ad slot cache refreshed');
    }
}