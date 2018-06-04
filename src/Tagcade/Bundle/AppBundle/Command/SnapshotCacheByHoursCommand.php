<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\SnapshotCacheEventCounterInterface;
use Tagcade\Service\Statistics\Statistics;
use Tagcade\Service\Statistics\Util\AccountReportCacheInterface;

class SnapshotCacheByHoursCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'tc:report:snapshot-by-hour';
    const KEY_DATE_TIME_FORMAT = 'H';

    /** @var SnapshotCacheEventCounterInterface */
    private $snapshotCacheEventCounter;

    private $slotIds;

    /** @var SymfonyStyle */
    private $io;

    /** @var AccountReportCacheInterface */
    private $accountReportCache;

    /** @var PublisherManagerInterface */
    private $publisherManager;

    /** @var Statistics */
    private $statistics;

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'date')
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
        $this->accountReportCache = $container->get('tagcade.service.statistics.util.account_report_cache');
        $this->publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $this->statistics = $container->get('tagcade.service.statistics');
        $this->io = new SymfonyStyle($input, $output);

        $now = date_create('now');

        $adSlots = $adSlotManager->all();
        foreach ($adSlots as $adSlot) {
            if (!$adSlot instanceof ReportableAdSlotInterface) {
                continue;
            }

            $publisher = $adSlot->getSite()->getPublisher();
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $publisherIsEnabled = $publisher->getUser()->isEnabled();
            // should execute for active publishers only
            if (!$publisherIsEnabled) {
                continue;
            }

            $this->slotIds = [];
            foreach($adSlot->getAdTags() as $adTag) {
                /** @var \Tagcade\Entity\Core\AdTag $adTag */
                $this->slotIds[$adTag->getId()] = $adSlot->getId();
            }

            $this->snapshotAdSlotCache($adSlot, $now);
        }

        $adTags = $adTagManager->all();
        foreach ($adTags as $adTag) {
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }

            $publisher = $adTag->getAdNetwork()->getPublisher();
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $publisherIsEnabled = $publisher->getUser()->isEnabled();
            // should execute for active publishers only
            if (!$publisherIsEnabled) {
                continue;
            }

            $this->snapshotAdTagCache($adTag, $now);
        }

        //    set time expire this hash field video_event_processor:event_count:180517
        try {
            $this->snapshotCacheEventCounter->setExpiredTimeForHashFieldDate();

            $this->io->success(sprintf("Success setting expire time for %s:%s", 'video_event_processor:event_count', $now->format('ymd')));

        } catch (\Exception $e) {

            $this->io->success(sprintf("Error when setting expire time for %s:%s", 'video_event_processor:event_count', $now->format('ymd')));

        }

        $dateInput = $input->getOption('date');
        $date = date_create($dateInput);

        if (!$date instanceof \DateTime) {
            $date = date_create('now');
        }

        $this->savePublisherDashboardHourlyToRedis($date);
        $this->savePlatformDashboardHourlyToRedis($date);
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

            $slotId = $this->getSlotIdForTag($adTag->getId());
            if ($slotId) {
                // 11) "CACHE_KEY_IN_BANNER_IMPRESSION adtag_1304:180507"
                $this->snapshotCacheEventCounter->snapshotAdTagInbannerImpressions($slotId, $adTag->getId(), $postFix);

                // 12) "CACHE_KEY_IN_BANNER_REQUEST y:adtag_1304:180507"
                $this->snapshotCacheEventCounter->snapshotAdTagInbannerRequest($slotId, $adTag->getId(), $postFix);

                // 13) "CACHE_KEY_IN_BANNER_TIMEOUT z:adtag_1304:180507"
                $this->snapshotCacheEventCounter->snapshotAdTagInbannerTimeOut($slotId, $adTag->getId(), $postFix);
            }

            $this->io->success(sprintf("Success snapshot cache for ad tag %s (ID: %s)", $adTag->getName(), $adTag->getId()));
        } catch (\Exception $e) {
            $this->io->error(sprintf("Error when snapshot cache for ad tag %s (ID: %s)", $adTag->getName(), $adTag->getId()));
        }
    }

    /**
     * @param ReportableAdSlotInterface $adSlot
     * @param \DateTime $time
     */
    private function snapshotAdSlotCache(ReportableAdSlotInterface $adSlot, \DateTime $time)
    {
        $postFix = $time->format(self::KEY_DATE_TIME_FORMAT);

        try {
            $this->snapshotCacheEventCounter->snapshotAdSlot($adSlot, $postFix);

            $this->io->success(sprintf("Success snapshot cache for ad slot %s (ID: %s)", $adSlot->getName(), $adSlot->getId()));
        } catch (\Exception $e) {
            $this->io->error(sprintf("Error when snapshot cache for ad slot %s (ID: %s)", $adSlot->getName(), $adSlot->getId()));
        }
    }

    private function getSlotIdForTag($tagId)
    {
        if (array_key_exists($tagId, $this->slotIds)) {
            return $this->slotIds[$tagId];
        }

        return null;
    }

    /**
     * @param \DateTime $date
     */
    private function savePublisherDashboardHourlyToRedis(\DateTime $date)
    {
        $publishers = $this->publisherManager->allActivePublishers();
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $accountReports = $this->statistics->getPublisherDashboardHourly($publisher, $date, $force = true);
            $this->accountReportCache->saveHourReports($accountReports);
            $this->io->success(sprintf("Successfully save publisher dash board hourly to redis (ID: %s)", $publisher->getId()));
        }
    }

    /**
     * @param \DateTime $date
     */
    private function savePlatformDashboardHourlyToRedis(\DateTime $date)
    {
        $this->io->text(sprintf("Start save platform dash board hourly to redis"));
        $platformReports = $this->statistics->getAdminDashboardHourly($date, $force = true);
        $this->accountReportCache->saveHourReports($platformReports);
        $this->io->success(sprintf("Successfully save platform dash board hourly to redis"));
    }
}
