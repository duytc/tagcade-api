<?php
namespace tagcade\dev;


use AppKernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

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
$sites = $siteRepository->findAll();

echo 'correcting...' . "\r\n";
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
    $siteCount++;

    if ($id % $batchSize == 0) {
        $em->flush();
    }
}

$em->flush();
$em->clear();

echo sprintf('%d site(s) updated', $siteCount) .  "\r\n";
echo 'DONE' . "\r\n";