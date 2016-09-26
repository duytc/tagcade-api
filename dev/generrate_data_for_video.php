<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

$container = $kernel->getContainer();

use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Entity\Core\VideoDemandPartner;
use Tagcade\Entity\Core\VideoPublisher;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User;
use Tagcade\Entity\Core\VideoWaterfallTag;
use Tagcade\Entity\Core\LibraryVideoDemandAdTag;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Entity\Core\VideoDemandAdTag;

/** @var \Doctrine\ORM\EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
$userManager = $container->get('tagcade_user.domain_manager.publisher');
$videoPublisherManager = $container->get('tagcade.domain_manager.video_waterfall_tag');
$videoDemandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
$videoDemandAdTagManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
$videoAdTagManager = $container->get('tagcade.domain_manager.video_waterfall_tag');

/** @var PublisherManagerInterface $publisherManager */
$publisherManager = $container->get('tagcade_user.domain_manager.publisher');

const NUM_PUBLISHER = 2;
const NUM_VIDEO_DEMAND_PARTNER = 3;
const NUM_VIDEO_PUBLISHER = 5;
const NUM_WATERFALL_TAG_PER_VIDEO_PUBLISHER = 20;
const NUM_VIDEO_DEMAND_AD_TAG_PER_WATERFALL = 20;

function xrange($max = 1000) {
    for ($i = 1; $i <= $max; $i++) {
        yield $i;
    }
}

// create publisher
foreach(xrange(NUM_PUBLISHER) as $publisherId) {
    $demandPartners = [];

    $publisher = new User();
    $username = sprintf('tctest%d', $publisherId);
    $publisher
        ->setUsername($username)
        ->setPlainPassword('123456')
        ->setEmail(sprintf('tctest%d@tagcade.com', $publisherId))
        ->setEnabled(true)
    ;

    $publisher->setCompany('tctest'); // doesn't return $this so cannot chain
    $publisher->setEnabledModules([$publisher::MODULE_DISPLAY, $publisher::MODULE_VIDEO]);

    $publisherManager->save($publisher);
    $em->flush();

    // create video publisher
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
        echo sprintf('finish inserting Video Publisher "%s"'. "\n", $videoPublisher->getName()) ;
        unset($tempObjs);
    }

    $em->detach($publisher);
    unset($publisher);
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