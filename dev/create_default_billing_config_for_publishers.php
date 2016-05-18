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
use Tagcade\Entity\Core\Site;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User;
use Tagcade\Model\User\Role\PublisherInterface;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

/** @var PublisherManagerInterface $userManager */
$userManager = $container->get('tagcade_user.domain_manager.publisher');
$allPublishers = $userManager->allActivePublishers();

/** @var PublisherInterface $publisher */
foreach($allPublishers as $publisher) {
    if (count($publisher->getBillingConfigs()) < 1) {
        $billingConfiguration = new BillingConfiguration();
        $billingConfiguration->setModule('DISPLAY_MODULE')
            ->setPublisher($publisher)
            ->setDefaultConfig(true)
            ->setBillingFactor('SLOT_OPPORTUNITIES')
            ->setTiers([]);
        $publisher->addBillingConfig($billingConfiguration);

        $userManager->save($publisher);
    }
}

