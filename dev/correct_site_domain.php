<?php
namespace tagcade\dev;


use AppKernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

const MODE_DRY = true; // dry = true => not persist to DB
const PUBLISHER_ID = 2;
const ALL_PLATFORM_SITES = false; // update all sites of all publishers. if set to true, this will ignore the defined PUBLISHER_ID

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

/** @var EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
$em->getConnection()->getConfiguration()->setSQLLogger(null);

/** @var SiteRepositoryInterface $siteRepository */
$siteRepository = $container->get('tagcade.repository.site');

/** @var PublisherManagerInterface $publisherManager */
$publisherManager = $container->get('tagcade_user.domain_manager.publisher');

function extractDomain($domain) {
    if (false !== stripos($domain, 'http')) {
        $domain = parse_url($domain, PHP_URL_HOST); // remove http part, get only domain
    }

    // remove the 'www' prefix
    if (0 === stripos($domain, 'www.')) {
        $domain = substr($domain, 4);
    }

    $slashPos = strpos($domain, '/');
    if (false !== $slashPos) {
        $domain = substr($domain, 0, $slashPos);
    }

    return $domain;
}

$batchSize = 20;
$siteCount = 0;
if (ALL_PLATFORM_SITES === TRUE) {
    $sites = $siteRepository->findAll();
    echo 'calculating for all sites...' . "\n";
}
else {
    $publisher = $publisherManager->find(PUBLISHER_ID);
    if (!$publisher instanceof PublisherInterface) {
        echo sprintf('the publisher %d does not exist', PUBLISHER_ID);
        exit;
    }

    $sites =  $siteRepository->getSitesForPublisher($publisher);
    echo sprintf('calculating for publisher "%s" (%s) :', $publisher->getFirstName(), $publisher->getEmail()) . "\n";
}

foreach($sites as $id=>$site) {
    /**
     * @var SiteInterface $site
     */
    if (!$site instanceof SiteInterface) {
        continue;
    }

    // correct domain
    $tmp = extractDomain($site->getDomain());
    if ($tmp === $site->getDomain()) {
        continue;
    }

    $siteCount++;
    if (MODE_DRY === FALSE) {
        $site->setDomain($tmp);

        // Set site unique for the case of auto create.
        for ($i = 0; $i < 10; $i++) {
            $hash = md5(sprintf('%d%s', $site->getPublisherId(), $site->getDomain()));
            $existingSites = $siteRepository->findBy(array('domain'=>$site->getDomain(), 'publisher' => $site->getPublisher()));
            $siteToken = $site->isAutoCreate() ?  $hash : (count($existingSites) < 2 ? $hash : uniqid(null, true));

            try {
                $site->setSiteToken($siteToken);
                break;
            }
            catch(\Exception $ex) {
                continue;
            }
        }

        if ($id % $batchSize == 0) {
            $em->flush();
        }

        echo sprintf('  - site "%s" (%s) updated successfully!', $site->getName(), $site->getDomain()) .  "\n";
    }
    else {
        echo sprintf('  - site "%s" (%s) is being updated to %s!', $site->getName(), $site->getDomain(), $tmp) .  "\n";
    }
}

if (MODE_DRY === FALSE) {
    $em->flush();
    $em->clear();
}

if ($siteCount === 0) {
    echo 'nothing to update' . "\n";
}
echo 'DONE' . "\n";

