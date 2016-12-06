<?php
namespace tagcade\dev;

use AppKernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\BillingConfiguration;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\LibraryVideoDemandAdTag;
use Tagcade\Entity\Core\Site;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Entity\Core\VideoDemandPartner;
use Tagcade\Entity\Core\VideoPublisher;
use Tagcade\Entity\Core\VideoWaterfallTag;
use Tagcade\Entity\Core\VideoWaterfallTagItem;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

// display module
const NUM_PUBLISHER = 3;
const NUM_SITES = 10;
const NUM_AD_SLOTS_PER_SITE = 10;
const NUM_EXCHANGE_PER_PUBLISHER = 10;

// video module
const NUM_VIDEO_PUBLISHER = 10;
const NUM_WATERFALL_TAG_PER_VIDEO_PUBLISHER = 10;
const NUM_VIDEO_DEMAND_AD_TAG_PER_WATERFALL = 5;

function xrange($max = 1000) {
    for ($i = 1; $i <= $max; $i++) {
        yield $i;
    }
}

/** @var EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
$em->getConnection()->getConfiguration()->setSQLLogger(null);
/** @var PublisherManagerInterface $publisherManager */
$publisherManager = $container->get('tagcade_user.domain_manager.publisher');

foreach(xrange(NUM_PUBLISHER) as $userId) {
    //create publisher
    $publisher = new User();
    $username = 'tagcade'.$userId;
    $publisher
        ->setUsername($username)
        ->setPlainPassword('123456')
        ->setEmail(sprintf('tctest%d@tagcade.com', $userId))
        ->setEnabled(true)
    ;

    $publisher->setCompany('tctest'); // doesn't return $this so cannot chain
    $enabledModules = [$publisher::MODULE_DISPLAY, $publisher::MODULE_RTB, $publisher::MODULE_VIDEO, $publisher::MODULE_HEADER_BIDDING, $publisher::MODULE_VIDEO_ANALYTICS, $publisher::MODULE_UNIFIED_REPORT];
    $publisher->setEnabledModules($enabledModules);

    $billingConfiguration = new BillingConfiguration();
    $billingConfiguration
        ->setPublisher($publisher)
        ->setModule($publisher::MODULE_DISPLAY)
        ->setDefaultConfig(true)
        ->setBillingFactor($billingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY);
    $publisher->addBillingConfig($billingConfiguration);

    $billingConfiguration = new BillingConfiguration();
    $billingConfiguration
        ->setPublisher($publisher)
        ->setModule($publisher::MODULE_VIDEO)
        ->setDefaultConfig(true)
        ->setBillingFactor($billingConfiguration::BILLING_FACTOR_VIDEO_VISIT);
    $publisher->addBillingConfig($billingConfiguration);

    $billingConfiguration = new BillingConfiguration();
    $billingConfiguration
        ->setPublisher($publisher)
        ->setModule($publisher::MODULE_HEADER_BIDDING)
        ->setDefaultConfig(true)
        ->setBillingFactor($billingConfiguration::BILLING_FACTOR_HEADER_BID_REQUEST);
    $publisher->addBillingConfig($billingConfiguration);

    $publisherManager->save($publisher);

    $em->flush();

    foreach(xrange(NUM_SITES) as $id) {
        gc_enable();

        $tempObjs = [];
        // create ad network
        $adNetwork = (new AdNetwork())
            ->setName('Test Ad Network ' . $id)
            ->setActiveAdTagsCount(0) // why do I have to do this? it should default to 0
            ->setPausedAdTagsCount(0)
            ->setPublisher($publisher);

        $em->persist($adNetwork);
        $tempObjs[] = $adNetwork;

        //create sites
        $site = (new Site())
            ->setName('Site ' . $id)
            ->setDomain(sprintf('site%d.com', $id))
            ->setAutoCreate(false)
            ->setEnableSourceReport(false)
            ->setPublisher($publisher)
        ;
        $em->persist($site);
        $tempObjs[] = $site;

        foreach(xrange(NUM_AD_SLOTS_PER_SITE) as $slotId) {
            // create ad slot
            $libraryAdSlot = (new LibraryDisplayAdSlot())
                ->setName("Display AdSlot " . $slotId)
                ->setType('display')
                ->setVisible(false)
                ->setPublisher($publisher);
            $adSlot = (new DisplayAdSlot())
                ->setLibraryAdSlot($libraryAdSlot)
                ->setAutoFit(true)
                ->setPassbackMode('position')
                ->setHeight(200)
                ->setWidth(400)
                ->setSite($site);

            $tempObjs[] = $adSlot;
            $tempObjs[] = $libraryAdSlot;
            // create ad tag
            $libraryAdTag = (new LibraryAdTag())->setName('ad tag 1')
                ->setVisible(false)
                ->setHtml('ad tag 1 html')
                ->setAdType(0)
                ->setAdNetwork($adNetwork)
                ->setInBannerDescriptor(array('platform' => null, 'timeout' => null, 'playerWidth' => null, 'playerHeight' => null, 'vastTags' => []));
            $tempObjs[] = $libraryAdTag;

            $adTag = (new AdTag())
                ->setLibraryAdTag($libraryAdTag)
                ->setAdSlot($adSlot)
                ->setActive(true)
                ->setFrequencyCap(11)
                ->setRefId(uniqid('', true));
            $adSlot->getAdTags()->add($adTag);
            $tempObjs[] = $adTag;

            unset($libraryAdTag);
            unset($adTag);

            $libraryAdTag = (new LibraryAdTag())->setName('ad tag 2')
                ->setVisible(false)
                ->setHtml('ad tag 2 html')
                ->setAdType(0)
                ->setAdNetwork($adNetwork)
                ->setInBannerDescriptor(array('platform' => null, 'timeout' => null, 'playerWidth' => null, 'playerHeight' => null, 'vastTags' => []));
            $tempObjs[] = $libraryAdTag;
            $adTag = (new AdTag())
                ->setLibraryAdTag($libraryAdTag)
                ->setAdSlot($adSlot)
                ->setActive(true)
                ->setFrequencyCap(11)
                ->setRefId(uniqid('', true));
            $adSlot->getAdTags()->add($adTag);
            $tempObjs[] = $adTag;
            unset($libraryAdTag);
            unset($adTag);
            $em->persist($adSlot);
            unset($adSlot);
            unset($libraryAdSlot);
        }

        unset($adNetwork);
        unset($site);

        $em->flush();
        foreach(array_reverse($tempObjs) as $obj) {
            $em->detach($obj);
        }


        gc_collect_cycles();

        echo sprintf('finish inserting site "Site %d"'. "\n", $id) ;
        unset($tempObjs);
    }

    foreach(xrange(NUM_VIDEO_PUBLISHER) as $videoPublisherId) {
        gc_enable();
        $tempObjs = [];

        $videoDemandPartner = (new VideoDemandPartner())
            ->setName(sprintf('Video Demand Partner %d - %s', $videoPublisherId, $username))
            ->setPublisher($publisher)
        ;

        $em->persist($videoDemandPartner);
        $tempObjs[] = $videoDemandPartner;

        $videoPublisher =
            (new VideoPublisher())
                ->setName(sprintf('Video Publisher %d - %s', $videoPublisherId, $username))
                ->setPublisher($publisher)
        ;

        $em->persist($videoPublisher);
        $tempObjs[] = $videoPublisher;

        // create waterfall tag
        foreach(xrange(NUM_WATERFALL_TAG_PER_VIDEO_PUBLISHER) as $waterfallTag) {
            $videoWaterfall = (new VideoWaterfallTag())
                ->setVideoPublisher($videoPublisher)
                ->setName(sprintf('Waterfall Tag %d - %s - %s', $waterfallTag, $videoPublisher->getName(), $username))
                ->setPlatform(['flash', 'js'])
                ->setUuid(generateUuidV4())
                ->setTargeting([])
            ;

            $em->persist($videoWaterfall);
            $tempObjs[] = $videoWaterfall;

            // create demand ad tag and demand partner
            foreach(xrange(NUM_VIDEO_DEMAND_AD_TAG_PER_WATERFALL) as $demandAdTag) {

                $videoWaterfallTagItem = (new VideoWaterfallTagItem())
                    ->setStrategy('linear')
                    ->setVideoWaterfallTag($videoWaterfall)
                    ->setPosition($demandAdTag)
                ;

                $tempObjs[] = $videoWaterfallTagItem;

                $videoDemandAdTagLibrary = (new LibraryVideoDemandAdTag())
                    ->setName(sprintf('Demand Ad Tag %d - %s - %s', $demandAdTag, $videoPublisher->getName(), $username))
                    ->setVideoDemandPartner($videoDemandPartner)
                    ->setTargeting([ 'countries' => [], 'exclude_countries' => [], 'domains' => [], 'exclude_domains' => [], 'platform' => [], 'player_size' => [], 'required_macros' => []])
                    ->setTimeout(3)
                    ->setTagURL('https://search.spotxchange.com/vast/2?content_page_url=${page_url}&player_width=${player_width}');

                $videoDemandAdTag = (new VideoDemandAdTag())
                    ->setLibraryVideoDemandAdTag($videoDemandAdTagLibrary)
                    ->setTargetingOverride(false)
                ;
                $videoDemandAdTag->setVideoWaterfallTagItem($videoWaterfallTagItem);

                $tempObjs[] = $videoDemandAdTag;
                $tempObjs[] = $videoDemandAdTagLibrary;

                $videoWaterfallTagItem->addVideoDemandAdTag($videoDemandAdTag);

                $em->persist($videoWaterfallTagItem);
                $tempObjs[] = $videoWaterfallTagItem;
            }
        }

        $em->flush();

        foreach(array_reverse($tempObjs) as $obj) {
            $em->detach($obj);
        }

        gc_collect_cycles();
        echo sprintf("\t". '- finish inserting Video Publisher "%s"'. "\n", $videoPublisher->getName()) ;
        unset($tempObjs);
    }

    $em->detach($publisher);
    unset($publisher);
    echo sprintf('finish inserting publisher "%s"' . "\n", 'tagcade' . $userId) ;
}

$em->flush();
$em->clear();


function generateUuidV4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,
        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}