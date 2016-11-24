<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
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
        /*
         * allow refresh video cache for one/all publisher(s) and one/all waterfall tag(s)
         * priority:
         * - all-publishers
         * - publisher
         * - all-waterfall-tags
         * - vid
         */
        $this
            ->setName('tc:cache:refresh-video-ad-tags')
            ->setDescription('Create initial ad slot cache if needed to avoid slams')
            ->addOption('publisher', 'p', InputOption::VALUE_OPTIONAL, 'update all video ad tags belongs to the given publisher')
            ->addOption('all-publishers', null, InputOption::VALUE_NONE, 'update all video ad tags belongs to the given publisher')
            ->addOption('vid', 'w', InputOption::VALUE_OPTIONAL, 'id of video ad tag whose cache is being to refreshed')
            ->addOption('all-waterfall-tags', null, InputOption::VALUE_NONE, 'id of video ad tag whose cache is being to refreshed');
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
        /* check if all publishers */
        $inputAllPublishers = $input->getOption('all-publishers');
        if ($inputAllPublishers === true) {
            $refreshedNumber = $this->refreshCacheForAllPublisher($output);

            $output->writeln(sprintf('%d items get refreshed totally', $refreshedNumber));

            return;
        }

        /* check if one publisher */
        $inputPublisherId = $input->getOption('publisher');
        if ($inputPublisherId === null) {
            throw new InvalidArgumentException('either option "all-publishers" or "publisher" must be set');
        }

        /** @var PublisherManagerInterface $publisherManager */
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');

        $publisher = $publisherManager->find($inputPublisherId);

        if (!$publisher instanceof PublisherInterface) {
            throw new InvalidArgumentException(sprintf('the publisher %d does not exist', $inputPublisherId));
        }

        /* check if all waterfall tags */
        $inputAllWaterfallTag = $input->getOption('all-waterfall-tags');
        if ($inputAllWaterfallTag === true) {
            $refreshedNumber = $this->refreshCacheForOnePublisher($publisher);

            $output->writeln(sprintf('%d items get refreshed', $refreshedNumber));

            return;
        }

        /* check if one waterfall tag */
        $inputWaterfallTagId = $input->getOption('vid');
        if ($inputWaterfallTagId === null) {
            throw new InvalidArgumentException('either option "all-waterfall-tags" or "vid" must be set');
        }

        /** @var VideoWaterfallTagManagerInterface $videoWaterfallTagManager */
        $videoWaterfallTagManager = $this->getContainer()->get('tagcade.domain_manager.video_waterfall_tag');

        $videoWaterfallTag = $videoWaterfallTagManager->find($inputWaterfallTagId);
        if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
            throw new InvalidArgumentException(sprintf('the video ad tag %d does not exist', $inputWaterfallTagId));
        }

        $refreshedNumber = $this->refreshCacheForOneWaterfallTag($videoWaterfallTag);

        $output->writeln(sprintf('%d items get refreshed totally', $refreshedNumber));
    }

    /**
     * refresh Cache For All Publisher
     * @param OutputInterface $output
     * @return int refreshed videoWaterfallTags
     */
    private function refreshCacheForAllPublisher(OutputInterface $output)
    {
        $totalRefreshedNumber = 0;

        /** @var PublisherManagerInterface $publisherManager */
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');

        /** @var Collection|PublisherInterface[] $publishers */
        $publishers = $publisherManager->allActivePublishers();

        foreach ($publishers as $publisher) {
            $refreshedNumber = $this->refreshCacheForOnePublisher($publisher);

            $totalRefreshedNumber += $refreshedNumber;

            $output->writeln(sprintf('%d items get refreshed for publisher %d', $refreshedNumber, $publisher->getId()));
        }

        return $totalRefreshedNumber;
    }

    /**
     * @param PublisherInterface $publisher
     * @return int refreshed videoWaterfallTags
     */
    private function refreshCacheForOnePublisher(PublisherInterface $publisher)
    {
        /** @var VideoWaterfallTagManagerInterface $videoWaterfallTagManager */
        $videoWaterfallTagManager = $this->getContainer()->get('tagcade.domain_manager.video_waterfall_tag');

        $videoWaterfallTags = $videoWaterfallTagManager->getVideoWaterfallTagsForPublisher($publisher);

        foreach ($videoWaterfallTags as $videoWaterfallTag) {
            $this->refreshCacheForOneWaterfallTag($videoWaterfallTag);
        }

        return count($videoWaterfallTags);
    }

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return int refreshed videoWaterfallTags
     */
    private function refreshCacheForOneWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        /** @var VideoWaterfallTagCacheRefresherInterface $videoWaterfallTagCacheManager */
        $videoWaterfallTagCacheManager = $this->getContainer()->get('tagcade.cache.video.refresher.video_waterfall_tag_cache_refresher');

        $videoWaterfallTagCacheManager->refreshVideoWaterfallTag($videoWaterfallTag);

        return 1;
    }
}