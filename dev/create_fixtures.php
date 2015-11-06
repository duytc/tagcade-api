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
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

const NUM_PUBLISHER = 3;
const NUM_SITES = 50;
const NUM_AD_SLOTS_PER_SITE = 100;

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
    $publisher
        ->setUsername('tagcade'.$userId)
        ->setPlainPassword('123456')
        ->setEmail(sprintf('tctest%d@tagcade.com', $userId))
        ->setEnabled(true)
    ;

    $publisher->setCompany('tctest'); // doesn't return $this so cannot chain
    $publisher->setEnabledModules([$publisher::MODULE_DISPLAY]);

    $publisherManager->save($publisher);

    foreach(xrange(NUM_SITES) as $id) {
        // create ad network
        $adNetwork = (new AdNetwork())
            ->setName('Test Ad Network ' . $id)
            ->setActiveAdTagsCount(0) // why do I have to do this? it should default to 0
            ->setPausedAdTagsCount(0)
            ->setPublisher($publisher);
        $em->persist($adNetwork);

        //create sites
        $site = (new Site())
            ->setName('Site ' . $id)
            ->setDomain(sprintf('site%d.com', $id))
            ->setAutoCreate(false)
            ->setEnableSourceReport(false)
            ->setPublisher($publisher)
        ;
        $em->persist($site);

        foreach(xrange(NUM_AD_SLOTS_PER_SITE) as $slotId) {
            // create ad slot
            $adSlot = (new DisplayAdSlot())
                ->setLibraryAdSlot(
                    (new LibraryDisplayAdSlot())
                        ->setName("Display AdSlot " . $slotId)
                        ->setType('display')
                        ->setVisible(false)
                        ->setPublisher($publisher)
                )
                ->setAutoFit(true)
                ->setPassbackMode('position')
                ->setHeight(200)
                ->setWidth(400)
                ->setSite($site);

            // create ad tag
            $adTag = (new AdTag())
                ->setLibraryAdTag(
                    (new LibraryAdTag())->setName('AdTag 1')
                        ->setVisible(false)
                        ->setHtml('ad tag 1 html')
                        ->setAdType(0)
                        ->setAdNetwork($adNetwork)
                )
                ->setAdSlot($adSlot)
                ->setActive(true)
                ->setFrequencyCap(11)
                ->setRefId(uniqid('', true));
            $adSlot->getAdTags()->add($adTag);

            $adTag = (new AdTag())
                ->setLibraryAdTag(
                    (new LibraryAdTag())->setName('AdTag 2')
                        ->setVisible(false)
                        ->setHtml('ad tag 2 html')
                        ->setAdType(0)
                        ->setAdNetwork($adNetwork)
                )
                ->setAdSlot($adSlot)
                ->setActive(true)
                ->setFrequencyCap(11)
                ->setRefId(uniqid('', true));
            $adSlot->getAdTags()->add($adTag);

            $em->persist($adSlot);
        }

        $em->flush();

        echo sprintf('finish inserting site "%s"'. "\n", $site->getName()) ;
    }
    echo sprintf('finish inserting publisher "%s"' . "\n", $publisher->getUsername()) ;
}



