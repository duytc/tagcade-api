<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Cache\TagCacheManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;

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
            ->setDescription('Create initial ad slot cache if needed to avoid slams')
            ->addOption(
                'publisher',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The publisher\'s id for updating all own ad slots'
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
        /** @var TagCacheManagerInterface $tagCacheManager */
        $tagCacheManager = $this->getContainer()->get('tagcade.cache.display.tag_cache_manager');

        // get publisher if has option
        $publisher = null;
        $publisherId = $input->getOption('publisher');

        if (null != $publisherId) {
            $publisher = $this->getContainer()->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);
            if (!$publisher instanceof PublisherInterface) {
                throw new InvalidArgumentException(sprintf('Publisher id %s does not exist', $publisherId));
            }
        }

        // refresh cache
        $tagCacheManager->refreshCache($publisher);

        $output->writeln('Ad slot cache is now refreshed');
    }
}
