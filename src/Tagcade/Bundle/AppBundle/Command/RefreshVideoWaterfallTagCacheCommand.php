<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * Provides a command-line interface for renewing cache using cli
 */
class RefreshVideoWaterfallTagCacheCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:cache:refresh-video-ad-tags')
            ->setDescription('Create initial ad slot cache if needed to avoid slams')
            ->addArgument('publisherId', InputArgument::REQUIRED, 'The publisher id')
            ->addOption('all', null, InputOption::VALUE_NONE, 'update all video ad tags belongs to the given publisher')
            ->addOption('vid', null, InputOption::VALUE_OPTIONAL, 'id of video ad tag whose cache is being to refreshed')
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
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $videoWaterfallTagManager = $this->getContainer()->get('tagcade.domain_manager.video_waterfall_tag');
        $publisherId = $input->getArgument('publisherId');

        $publisher = $publisherManager->find($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            throw new InvalidArgumentException(sprintf('The publisher %d does not exist', $publisherId));
        }

        $all = $input->getOption('all');
        $vid = $input->getOption('vid');
        $videoWaterfallTags = [];
        if ($all === true) {
            $videoWaterfallTags = $videoWaterfallTagManager->getVideoWaterfallTagsForPublisher($publisher);
        } else {
            if ($vid === null) {
                throw new InvalidArgumentException('either option "all" or "vid" must be set');
            }
            $videoWaterfallTag = $videoWaterfallTagManager->find($vid);
            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                throw new InvalidArgumentException(sprintf('The video ad tag %d does not exist', $vid));
            }
            $videoWaterfallTags[] = $videoWaterfallTag;
        }

        $videoWaterfallTagCacheManager = $this->getContainer()->get('tagcade.cache.video.refresher.video_waterfall_tag_cache_refresher');
        foreach($videoWaterfallTags as $videoWaterfallTag) {
            $videoWaterfallTagCacheManager->refreshVideoWaterfallTag($videoWaterfallTag);
        }

        $output->writeln(sprintf('%d items get refreshed', count($videoWaterfallTags)));
    }
}
