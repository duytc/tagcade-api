<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\SnapshotCacheEventCounterInterface;

class SnapshotCacheByHoursCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'tc:report:snapshot-by-hour';
    const KEY_DATE_TIME_FORMAT = 'H';

    /** @var SnapshotCacheEventCounterInterface */
    private $snapshotCacheEventCounter;

    /** @var SymfonyStyle */
    private $io;
    
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Snapshot live ad slot/ad tag report in redis by hour, support show dashboard chart day-over-day');
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
        $container = $this->getContainer();
        $adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
        $adTagManager = $container->get('tagcade.domain_manager.ad_tag');
        $this->snapshotCacheEventCounter = $container->get('tagcade.service.report.performance_report.display.counter.snapshot_cache_event_counter');
        $this->io = new SymfonyStyle($input, $output);

        $now = date_create('now');

        $adSlots = $adSlotManager->all();
        foreach ($adSlots as $adSlot) {
            if (!$adSlot instanceof BaseAdSlotInterface) {
                continue;
            }

            $this->snapshotAdSlotCache($adSlot, $now);
        }

        $adTags = $adTagManager->all();
        foreach ($adTags as $adTag) {
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }

            $this->snapshotAdTagCache($adTag, $now);
        }
    }

    /**
     * @param AdTagInterface $adTag
     * @param \DateTime $time
     */
    private function snapshotAdTagCache(AdTagInterface $adTag, \DateTime $time)
    {
        $postFix = $time->format(self::KEY_DATE_TIME_FORMAT);

        try {
            // 1) "refreshes:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotRefreshesCount($adTag->getId(), $postFix);

            // 2) "blank_impressions:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotBlankImpressionCount($adTag->getId(), $postFix);

            // 3) "void_impressions:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotVoidImpressionCount($adTag->getId(), $postFix);

            // 4) "opportunities:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotOpportunitiesCount($adTag->getId(), $postFix);

            // 5) "clicks:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotClicksCount($adTag->getId(), $postFix);

            // 6) "impressions:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotImpressionsCount($adTag->getId(), $postFix);

            // 7) "verified_impressions:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotVerifyImpressionsCount($adTag->getId(), $postFix);

            // 8) "passbacks:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotPassbacksCount($adTag->getId(), $postFix);

            // 9) "unverified_impressions:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotUnVerifyImpressionsCount($adTag->getId(), $postFix);

            // 10) "first_opportunities:adtag_1304:180507"
            $this->snapshotCacheEventCounter->snapshotFirstOpportunitiesCount($adTag->getId(), $postFix);

            $this->io->success(sprintf("Success snapshot cache for ad tag %s (ID: %s)", $adTag->getName(), $adTag->getId()));
        } catch (\Exception $e) {
            $this->io->error(sprintf("Error when snapshot cache for ad tag %s (ID: %s)", $adTag->getName(), $adTag->getId()));
        }
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @param \DateTime $time
     */
    private function snapshotAdSlotCache(BaseAdSlotInterface $adSlot, \DateTime $time)
    {
        $postFix = $time->format(self::KEY_DATE_TIME_FORMAT);

        try {
            //1) "opportunities:adslot_478:180507"
            $this->snapshotCacheEventCounter->snapshotSlotOpportunitiesCount($adSlot->getId(), $postFix);

            $this->io->success(sprintf("Success snapshot cache for ad slot %s (ID: %s)", $adSlot->getName(), $adSlot->getId()));
        } catch (\Exception $e) {
            $this->io->error(sprintf("Error when snapshot cache for ad slot %s (ID: %s)", $adSlot->getName(), $adSlot->getId()));
        }
    }
}
