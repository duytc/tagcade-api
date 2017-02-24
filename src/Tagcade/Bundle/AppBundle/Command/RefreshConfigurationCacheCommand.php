<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Cache\ConfigurationCacheInterface;
use Tagcade\DomainManager\RonAdSlotManagerInterface;

/**
 * Provides a command-line interface for renewing cache using cli
 */
class RefreshConfigurationCacheCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:cache:refresh-config')
            ->setDescription('refresh configuration cache for ron slots');;
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
        /**
         * @var RonAdSlotManagerInterface $ronAdSlotManager
         */
        $ronAdSlotManager = $this->getContainer()->get('tagcade.domain_manager.ron_ad_slot');
        /**
         * @var ConfigurationCacheInterface $configCache
         */
        $configCache = $this->getContainer()->get('tagcade.cache.app.configuration_cache');
        $ronAdSlots = $ronAdSlotManager->all();
        if ($ronAdSlots instanceof PersistentCollection) {
            $ronAdSlots = $ronAdSlots->toArray();
        }

        $configCache->refreshForRonAdSlots($ronAdSlots);

        $output->writeln('event processor configuration cache is now refreshed');
    }
}
