<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

$container = $kernel->getContainer();

/** @var \Doctrine\ORM\EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
$userManager = $container->get('tagcade_user.domain_manager.publisher');
$videoPublisherManager = $container->get('tagcade.domain_manager.video_waterfall_tag');
$videoDemandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
$videoDemandAdTagManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
$videoAdTagManager = $container->get('tagcade.domain_manager.video_waterfall_tag');

$dataNumber = [
    'publisher' => 20,
    'videoDemandPartner' => 20,
    'videoPublisherForPublisher' => 20,
    'videoWaterfallForVideoPublisher' => 20,
    'videoDemandAdTagForWaterfall' => 20
];

$nameDefault = [
    'publisher' => 'publisher',
    'videoPublisher' => 'Video Publisher',
    'videoDemandPartner' => 'Video Demand Partner',
    'videoWaterfall' => 'Video Waterfall',
    'videoDemandAdTag' => 'Video Demand Ad Tag'
];

// create publisher
for ($p = 1; $p <= $dataNumber['publisher']; $p++) {
    $demandPartners = [];

    $publisher = new Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User();
    $username = $nameDefault['publisher'].$p; // set name for publisher

    $publisher->setUsername($username);
    $publisher->setUsernameCanonical($username);
    $publisher->setPassword($username);
    $publisher->setEmail($username.'@gmail.com');
    $publisher->setCompany($username);
    $publisher->setEnabledModules([$publisher::MODULE_DISPLAY, $publisher::MODULE_VIDEO]);
    $publisher->setEnabled(true);

    $em->persist($publisher);

    echo sprintf('create publisher %s' . "\n", $username) ;

    // create video demand partner
    for ($vd = 1; $vd <= $dataNumber['videoDemandPartner']; $vd++) {
        $videoDemandPartner = new Tagcade\Entity\Core\VideoDemandPartner();
        $videoDemandPartnerName = $nameDefault['videoDemandPartner'].$vd . '-' . $username; // set name for video demand partner

        $videoDemandPartner->setName($videoDemandPartnerName);
        $videoDemandPartner->setPublisher($publisher);
        $em->persist($videoDemandPartner);

        $demandPartners[] = $videoDemandPartner;
        echo sprintf('create video demand partner %s' . "\n", $videoDemandPartnerName);
    }

    // create video publisher
    for ($vp = 1; $vp <= $dataNumber['videoPublisherForPublisher']; $vp++) {
        gc_enable();

        $tempObjs = [];

        $videoPublisher = new Tagcade\Entity\Core\VideoPublisher();
        $videoPublisherName = $nameDefault['videoPublisher'].$vp . '-' . $username; // set name for video publisher

        $videoPublisher->setName($videoPublisherName);
        $videoPublisher->setPublisher($publisher);

        $em->persist($videoPublisher);

        $tempObjs[] = $videoPublisher;

        echo sprintf('create video publisher %s' . "\n", $videoPublisherName) ;

        // create waterfall tag
        for ($vw = 1; $vw <= $dataNumber['videoWaterfallForVideoPublisher']; $vw++) {
            $videoWaterfall = new Tagcade\Entity\Core\VideoWaterfallTag();
            $videoWaterfallName = $nameDefault['videoWaterfall'].$vw . '-' . $videoPublisherName; // set name for video waterfall tag

            $videoWaterfall->setVideoPublisher($videoPublisher);
            $videoWaterfall->setName($videoWaterfallName);
            $videoWaterfall->setPlatform(['flash', 'js']);
            $videoWaterfall->setUuid(generateUuidV4());
            $videoWaterfall->setTargeting([]);

            $em->persist($videoWaterfall);
            $tempObjs[] = $videoWaterfall;

            echo sprintf('create video waterfall tag %s' . "\n", $videoWaterfallName);

            // create demand ad tag and demand partner
            for ($vdt = 1; $vdt <= $dataNumber['videoDemandAdTagForWaterfall']; $vdt++) {
                $videoDemandAdTag = new Tagcade\Entity\Core\VideoDemandAdTag();
                $videoWaterfallTagItem = new Tagcade\Entity\Core\VideoWaterfallTagItem();
                $videoDemandAdTagLibrary = new Tagcade\Entity\Core\LibraryVideoDemandAdTag();

                $videoDemandAdTagName = $nameDefault['videoDemandAdTag'].$vdt . '-' . $videoWaterfallName; // set name for video demand ad tag

                // create library demand ad tag
                $videoDemandAdTagLibrary->setName($videoDemandAdTagName);
                $videoDemandAdTagLibrary->setVideoDemandPartner($demandPartners[rand(0, $dataNumber['videoDemandPartner'] -1)]);
                $videoDemandAdTagLibrary->setTargeting([ 'countries' => [], 'exclude_countries' => [], 'domains' => [], 'exclude_domains' => [], 'platform' => [], 'player_size' => [], 'required_macros' => []]);
                $videoDemandAdTagLibrary->setTimeout(3);
                $videoDemandAdTagLibrary->setTagURL('https://search.spotxchange.com/vast/2?content_page_url=${page_url}&player_width=${player_width}');
                $em->persist($videoDemandAdTagLibrary);
                $tempObjs[] = $videoDemandAdTagLibrary;

                echo sprintf('create library video demand ad tag %s' . "\n", $videoDemandAdTagName);

                // create $videoWaterfallTagItem
                $videoWaterfallTagItem->setStrategy('linear');
                $videoWaterfallTagItem->setPosition($vdt-1);
                $videoWaterfallTagItem->setVideoWaterfallTag($videoWaterfall);
                $em->persist($videoWaterfallTagItem);
                $tempObjs[] = $videoWaterfallTagItem;

                // crete demand ad tag from library demand ad tag
                $videoDemandAdTag->setLibraryVideoDemandAdTag($videoDemandAdTagLibrary);
                $videoDemandAdTag->setTargetingOverride(false);
                $videoDemandAdTag->setVideoWaterfallTagItem($videoWaterfallTagItem);
                $em->persist($videoDemandAdTag);
                $tempObjs[] = $videoDemandAdTag;

                echo sprintf('create video demand ad tag %s' . "\n", $videoDemandAdTagName);
            }
        }

        $em->flush();

        foreach(array_reverse($tempObjs) as $obj) {
            $em->detach($obj);
        }

        gc_collect_cycles();
    }

    $em->flush();

    foreach(array_reverse($demandPartners) as $obj) {
        $em->detach($obj);
    }

    $em->detach($publisher);
    unset($publisher);
}

$em->flush();

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