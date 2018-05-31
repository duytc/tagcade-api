<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Service\Report\VideoReport\Counter\SnapshotVideoCacheEventCounterInterface;

class SnapshotCacheVideoReportByHoursCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'tc:video-report:snapshot-by-hour';
    const KEY_DATE_TIME_FORMAT = 'H';

    /** @var SnapshotVideoCacheEventCounterInterface */
    private $snapshotVideoCacheEventCounter;

    /** @var SymfonyStyle */
    private $io;

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Snapshot live waterfall/ demand tag video report in redis by hour, support show dashboard chart day-over-day');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $videoWaterfallTagManager = $container->get('tagcade.domain_manager.video_waterfall_tag');
        $videoDemandAdTagManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
        $this->snapshotVideoCacheEventCounter = $container->get('tagcade.service.report.video_report.counter.snapshot_video_cache_event_counter');
        $this->io = new SymfonyStyle($input, $output);

        $now = date_create('now');

        $videoWaterfallTags = $videoWaterfallTagManager->all();
        foreach ($videoWaterfallTags as $videoWaterfallTag) {
            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            $publisherIsEnabled = $videoWaterfallTag->getPublisher()->isEnabled();
            // should execute for active publishers only
            if (!$publisherIsEnabled) {
                continue;
            }
            /* 1. generate cache key hour for waterfallTag */
            $this->snapshotWaterfallTagCache($videoWaterfallTag, $now);

            /* 2. get all demandAdTags from WaterfallTag */
            $demandAdTags = $videoDemandAdTagManager->getVideoDemandAdTagsForVideoWaterfallTag($videoWaterfallTag);

            /* 3. generate cache key hour for demandAdTag */
            foreach ($demandAdTags as $index => $demandAdTag) {

                if (!$demandAdTag instanceof VideoDemandAdTagInterface) {
                    continue;
                }
                $this->snapshotDemandTagCache($demandAdTag, $now);
            }
        }

        //    set time expire this hash field video_event_processor:event_count:180517
        try {
            $this->snapshotVideoCacheEventCounter->setExpiredTimeForHashFieldDate();

            $this->io->success(sprintf("Success setting expire time for %s:%s", 'video_event_processor:event_count', $now->format('ymd')));

        } catch (\Exception $e) {

            $this->io->success(sprintf("Error when setting expire time for %s:%s", 'video_event_processor:event_count', $now->format('ymd')));

        }

    }

    /**
     * @param VideoDemandAdTagInterface $demandAdTag
     * @param \DateTime $time
     */
    private function snapshotDemandTagCache(VideoDemandAdTagInterface $demandAdTag, \DateTime $time)
    {
        $postFix = $time->format(self::KEY_DATE_TIME_FORMAT);

        try {
            $this->snapshotVideoCacheEventCounter->snapshotDemandAdTag($demandAdTag->getId(), $postFix);
            $this->io->success(sprintf("Success snapshot cache for demand tag %s (ID: %s)", $demandAdTag->getName(), $demandAdTag->getId()));
        } catch (\Exception $e) {
            $this->io->error(sprintf("Error when snapshot cache for demand tag %s (ID: %s)", $demandAdTag->getName(), $demandAdTag->getId()));
        }
    }

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param \DateTime $time
     */
    private function snapshotWaterfallTagCache(VideoWaterfallTagInterface $videoWaterfallTag, \DateTime $time)
    {
        $postFix = $time->format(self::KEY_DATE_TIME_FORMAT);

        try {
            $this->snapshotVideoCacheEventCounter->snapshotWaterfallTag($videoWaterfallTag->getUuid(), $postFix);

            $this->io->success(sprintf("Success snapshot cache for watefall tag %s (ID: %s)", $videoWaterfallTag->getName(), $videoWaterfallTag->getId()));
        } catch (\Exception $e) {
            $this->io->error(sprintf("Error when snapshot cache for ad slot %s (ID: %s)", $videoWaterfallTag->getName(), $videoWaterfallTag->getId()));
        }
    }
}
