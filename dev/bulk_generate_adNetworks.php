<?php

$loader = require_once __DIR__.'/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\User\Role\PublisherInterface;

$userManager = $container->get('tagcade_user.domain_manager.user');
$adNetworkManager = $container->get('tagcade.domain_manager.ad_network');

/** @var UserEntityInterface $user */
$user = $userManager->find(2);
if (!$user instanceof PublisherInterface) {
     throw new \Tagcade\Exception\InvalidArgumentException('Expect publisher');
}


for($i=0; $i < 2; $i++){
    $name = 'adNetwork' . '-' . (new DateTime('now'))->format('YmdHis') . '-' .$i;
    $url = 'http://' . $name . '.com';

    /** @var \Tagcade\Model\Core\AdNetworkInterface $adNetwork */
    $adNetwork = $adNetworkManager->createNew();
    $adNetwork->setName($name);
    $adNetwork->setUrl($url);
    $adNetwork->setDefaultCpmRate(1.0);
    $adNetwork->setPublisher($user);

    $adNetworkManager->save($adNetwork);
}
