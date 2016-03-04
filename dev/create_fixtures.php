<?php
namespace tagcade\dev;

use AppKernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Entity\Core\Exchange;
use Tagcade\Entity\Core\PublisherExchange;
use Tagcade\Model\Core\ExchangeInterface;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

const NUM_PUBLISHER = 3;
const NUM_SITES = 3;
const NUM_AD_SLOTS_PER_SITE = 10;
const NUM_EXCHANGE_PER_PUBLISHER = 10;

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
$exchangeManager = $container->get('tagcade.domain_manager.exchange');

$exchanges = [];
foreach(xrange(NUM_EXCHANGE_PER_PUBLISHER) as $exchangeId) {
    $exchange = new Exchange();
    $exchange->setName(sprintf("Index Exchange %d", $exchangeId));
    $exchange->setCanonicalName(sprintf('index-exchange-%d', $exchangeId));
    $em->persist($exchange);
    $exchanges[] = $exchange;
}
$em->flush();

foreach(xrange(NUM_PUBLISHER) as $userId) {
    //create publisher
    $publisher = new User();
    $publisher
        ->setUsername('tagcade'.$userId)
        ->setPlainPassword('123456')
        ->setEmail(sprintf('tctest%d@tagcade.com', $userId))
        ->setEnabled(true)
    ;
    /**
     * @var ExchangeInterface $exchange
     */
    foreach($exchanges as $exchange) {
        $publisherExchange = new PublisherExchange();
        $publisherExchange->setPublisher($publisher);
        $publisherExchange->setExchange($exchange);

        $publisher->addPublisherExchanges($publisherExchange);
    }

    $publisher->setCompany('tctest'); // doesn't return $this so cannot chain
    $publisher->setEnabledModules([$publisher::MODULE_DISPLAY, $publisher::MODULE_RTB]);

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
            $libraryAdTag = (new LibraryAdTag())->setName('AdTag 1')
                ->setVisible(false)
                ->setHtml('ad tag 1 html')
                ->setAdType(0)
                ->setAdNetwork($adNetwork);
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

            $libraryAdTag = (new LibraryAdTag())->setName('AdTag 2')
                ->setVisible(false)
                ->setHtml('ad tag 2 html')
                ->setAdType(0)
                ->setAdNetwork($adNetwork);
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
    $em->detach($publisher);
    unset($publisher);
    echo sprintf('finish inserting publisher "%s"' . "\n", 'tagcade' . $userId) ;
}

$em->flush();
$em->clear();
