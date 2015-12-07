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
$siteManager = $container->get('tagcade.domain_manager.site');

/** @var UserEntityInterface $user */
$user = $userManager->find(2);
if (!$user instanceof PublisherInterface) {
     throw new \Tagcade\Exception\InvalidArgumentException('Expect publisher');
}


for($i=0; $i < 2; $i++){
    $name = 'site' . '-' . (new DateTime('now'))->format('YmdHis') . '-' .$i;
    $domain = 'http://' . $name . '.com';

    /** @var \Tagcade\Model\Core\SiteInterface $site */
    $site = $siteManager->createNew();
    $site->setName($name);
    $site->setDomain($domain);
    $site->setEnableSourceReport(true);
    $site->setPublisher($user);

    $siteManager->save($site);
}
