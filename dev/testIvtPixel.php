<?php
namespace tagcade\dev;

use AppKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Entity\Core\IvtPixel;
use Tagcade\Entity\Core\IvtPixelWaterfallTag;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

/** Get services */
$ivtPixelManager = $container->get('tagcade.domain_manager.ivt_pixel');
$ivtPixelWaterfallTagManager = $container->get('tagcade.domain_manager.ivt_pixel_waterfall_tag');
$videoWaterfallTagManager = $container->get('tagcade.domain_manager.video_waterfall_tag');
$publisherManager = $container->get('tagcade_user.domain_manager.publisher');

/** @var PublisherInterface $publisher */
$publisher = $publisherManager->find(5);
/** @var VideoWaterfallTagInterface $videoWaterfallTag */
$videoWaterfallTag = $videoWaterfallTagManager->find(2);

/**
 * Creating sample Ivt Pixel
 */
$ivtPixel = new IvtPixel();
$ivtPixel->setName('Ivt sample');
$ivtPixel->setPublisher($publisher);
$ivtPixel->setFireOn('impression');
$ivtPixel->setRunningLimit(12);
$ivtPixel->setUrls([
    "http://project4gallery.com/wp-content/uploads/2016/03/5Bwallcoo_com5D_Beautiful20Nature2020HD20Landscape2020.jpg",
    "https://s-media-cache-ak0.pinimg.com/736x/90/e9/e7/90e9e7fb4fcfee8f35535a24dd8598bf--rayban-cool-ideas.jpg"
]);
$ivtPixelManager->save($ivtPixel);

/**
 * Creating Ivt waterfall tag
 */
$ivtPixelWaterfallTag = new IvtPixelWaterfallTag();
$ivtPixelWaterfallTag->setIvtPixel($ivtPixel);
$ivtPixelWaterfallTag->setWaterfallTag($videoWaterfallTag);
$ivtPixelWaterfallTagManager->save($ivtPixelWaterfallTag);

$ivtPixel->setIvtPixelWaterfallTags([$ivtPixelWaterfallTag]);

/**
 * Test update Ivt Pixel
 */
$ivtPixel->setFireOn('new fire on');
$ivtPixel->setUrls(["new url1", "new url2"]);
$ivtPixel->setRunningLimit(25);
$ivtPixelManager->save($ivtPixel);

/**
 * Test unMap Ivt Pixel from VideoWaterfallTag
 */
$videoWaterfallTag->setIvtPixelWaterfallTags([]);
$videoWaterfallTagManager->save($videoWaterfallTag);

/**
 * Test delete Ivt Pixel
 */
$ivtPixelManager->delete($ivtPixel);

$a = 4;