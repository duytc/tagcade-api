<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
use \DateTime;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class SetInitialValueForLiveAccountReportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:live-account-report:initialize')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'the date to calculate data in Y-m-d format')
            ->setDescription('Create initial data for live account report or recalculate data from ad slot live data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = $input->getOption('date');
        if (empty($date)) {
            $date = new DateTime('yesterday');
        } else if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
            throw new InvalidArgumentException('expect date format to be "YYYY-MM-DD"');
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $date);
        }

        $container = $this->getContainer();
        $logger = $container->get('logger');
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
        $adTagManager = $container->get('tagcade.domain_manager.ad_tag');


        //performance report
        $eventCounter = $container->get('tagcade.service.report.performance_report.display.counter.cache_event_counter');
        $eventCounter->setDate($date);
        $cache = $container->get('tagcade.legacy.cache.performance_report_data');


        $activePublishers = $publisherManager->allPublisherWithDisplayModule();
        /**
         * @var PublisherInterface $publisher
         */
        foreach($activePublishers as $publisher) {
            $logger->info(sprintf('initialize data for publisher %s', $publisher->getUser()->getUsername()));
            $publisherData = array (
                $eventCounter::CACHE_KEY_ACC_OPPORTUNITY => 0,
                $eventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY => 0,
                $eventCounter::CACHE_KEY_IMPRESSION => 0,
                $eventCounter::CACHE_KEY_PASSBACK => 0,
                $eventCounter::CACHE_KEY_HB_BID_REQUEST => 0,
            );

            $adSlots = $adSlotManager->getAdSlotsForPublisher($publisher);
            $adSlotIds = array_map(function(BaseAdSlotInterface $adSlot) {
                return $adSlot->getId();
            }, $adSlots);

            $result = $eventCounter->getAdSlotReports($adSlotIds);

            foreach($result as $id => $item) {
                $publisherData[$eventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY] += $item->getSlotOpportunities();
                $publisherData[$eventCounter::CACHE_KEY_HB_BID_REQUEST] += $item->getHbRequests();
            }

            $adTagIds = $adTagManager->getActiveAdTagsIdsForPublisher($publisher);

            $result = $eventCounter->getAdTagReports($adTagIds);
            foreach($result as $id => $item) {
                $publisherData[$eventCounter::CACHE_KEY_ACC_OPPORTUNITY] += $item->getOpportunities();
                $publisherData[$eventCounter::CACHE_KEY_IMPRESSION] += $item->getImpressions();
                $publisherData[$eventCounter::CACHE_KEY_PASSBACK] += $item->getPassbackCount();
            }

            //save to cache
            $cache->save(
                $eventCounter->getCacheKey(
                    $eventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY,
                    $eventCounter->getNamespace($eventCounter::NAMESPACE_ACCOUNT, $publisher->getId())
                ),
                $publisherData[$eventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY]
            );

            $cache->save(
                $eventCounter->getCacheKey(
                    $eventCounter::CACHE_KEY_ACC_OPPORTUNITY,
                    $eventCounter->getNamespace($eventCounter::NAMESPACE_ACCOUNT, $publisher->getId())
                ),
                $publisherData[$eventCounter::CACHE_KEY_ACC_OPPORTUNITY]
            );

            $cache->save(
                $eventCounter->getCacheKey(
                    $eventCounter::CACHE_KEY_IMPRESSION,
                    $eventCounter->getNamespace($eventCounter::NAMESPACE_ACCOUNT, $publisher->getId())
                ),
                $publisherData[$eventCounter::CACHE_KEY_IMPRESSION]
            );

            $cache->save(
                $eventCounter->getCacheKey(
                    $eventCounter::CACHE_KEY_PASSBACK,
                    $eventCounter->getNamespace($eventCounter::NAMESPACE_ACCOUNT, $publisher->getId())
                ),
                $publisherData[$eventCounter::CACHE_KEY_PASSBACK]
            );

            $cache->save(
                $eventCounter->getCacheKey(
                    $eventCounter::CACHE_KEY_HB_BID_REQUEST,
                    $eventCounter->getNamespace($eventCounter::NAMESPACE_ACCOUNT, $publisher->getId())
                ),
                $publisherData[$eventCounter::CACHE_KEY_HB_BID_REQUEST]
            );
        }

        $logger->info('Done');
    }
}