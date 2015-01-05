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

$tagGenerator = new \Tagcade\Service\TagGenerator('http://tags.tagcade.com');

/** @var UserEntityInterface $user */
$user = $userManager->find(7);
if (!$user instanceof PublisherInterface) {
     throw new \Tagcade\Exception\InvalidArgumentException('Expect publisher');
}


$sites = $siteManager->getSitesForPublisher($user);

function getDiv($sep = '=', $spacing = 1)
{
    $blankLines = str_repeat("\n", (int) $spacing);

    return $blankLines . str_repeat($sep, 30) . $blankLines;
}

function getSection($title, $subtitle, $content)
{
    $text = $title;
    $text .= getDiv('-');
    $text .= $subtitle;
    $text .= "\n\n";
    $text .= $content;
    $text .= getDiv('=', 3);

    return $text;
}

foreach($sites as $site) {
    $tags = $tagGenerator->getTagsForSite($site);

    if (empty($tags)) {
        continue;
    }

    $text = getSection(
        'Header Include',
        'Copy and paste the following tag into the <head> section of your website.',
        $tags['header']
    );

    $text .= getSection(
        'Display Passback Tag',
        'Give the following tag to your ad networks as a passback/default/fallback tag for display ads.' . "\n",
        $tags['display']['passback']
    );

    $text .= getSection(
        'Display Ad Tags',
        'Copy and paste the following ad tags into the &lt;body&gt; section of your website.',
        join("\n\n", $tags['display']['ad_slots'])
    );

    file_put_contents(sprintf('tags/%d-%s.txt', $site->getId(), $site->getDomain()), $text);

    unset($text);
}