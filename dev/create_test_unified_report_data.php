<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

use Tagcade\Model\User\Role\PublisherInterface;
/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();

/**
 * @var \Doctrine\ORM\EntityManagerInterface $em
 */
$em = $container->get('doctrine.orm.entity_manager');

/**
 * @var \Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface $userManager
 */
$userManager = $container->get('tagcade_user.domain_manager.publisher');
/**
 * @var \Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface $subPublisherManager
 */
$subPublisherManager = $container->get('tagcade_user.domain_manager.sub_publisher');

$reportPartnerAccountBuilder = $container->get('tagcade.repository.report.performance_report.display.hierarchy.partner.account');
$reportPartnerAdTagBuilder = $container->get('tagcade.repository.report.performance_report.display.hierarchy.partner.ad_network_ad_tag');
$reportPartnerDomainAdTagBuilder = $container->get('tagcade.repository.report.performance_report.display.hierarchy.partner.ad_network_domain_ad_tag');
/**
 * @var \Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportRepository $reportPartnerAdNetworkRepository
 */
$reportPartnerAdNetworkRepository = $container->get('tagcade.repository.report.performance_report.display.hierarchy.ad_network.ad_network');
$reportPartnerDomainBuilder = $container->get('tagcade.repository.report.performance_report.display.hierarchy.partner.ad_network_domain');
$reportComparisonCreator = $container->get('tagcade.service.report.unified_report.report_comparison_creator');
$reportAdTagAdNetworkSubPublisherRepository = $container->get('tagcade.repository.report.unified_report.network.network_ad_tag_sub_publisher_report');

/**
 * @var \Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportRepositoryInterface $subPublisherNetworkReportRepository
 */
$subPublisherNetworkReportRepository = $container->get('tagcade.repository.report.performance_report.display.hierarchy.sub_publisher.sub_publisher_ad_network');
/**
 * @var \Tagcade\DomainManager\AdNetworkManagerInterface $adNetworkDomainManager
 */
$adNetworkDomainManager = $container->get('tagcade.domain_manager.ad_network');

$adTagRepository = $container->get('tagcade.repository.ad_tag');
$siteRepository = $container->get('tagcade.repository.site');

$begin = new DateTime('2016-04-01');
$end = new DateTime('2016-04-05');

$today = new DateTime('today');
if ($end >= $today) {
    $end = new DateTime('yesterday');
}

$unifiedReportMaxVariation = 10; // variation percentage between tagcade and third party data
$unifiedReportMinCpm = 1; // cent value
$unifiedReportMaxCpm = 10; // cent value

$publisherId = 2; // Id of publisher to generate data
$publisher = $userManager->findPublisher($publisherId);
if (!$publisher instanceof \Tagcade\Model\User\Role\PublisherInterface) {
    throw new Exception(sprintf('Not found that publisher %d', $publisherId));
}

$allPublishers = [$publisher];

$end = $end->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($begin, $interval ,$end);

$em->getConnection()->getConfiguration()->setSQLLogger(null);


function setPartnerReport(Tagcade\Model\Report\UnifiedReport\ReportInterface $partnerReport, Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface $myReport, $variation, $revenue = null)
{
    global $unifiedReportMinCpm, $unifiedReportMaxCpm;

    $isHigherVariation = -1;
    $remainingPercentage = 1-$variation;
//    $variation = 0;

    $partnerReport->setTotalOpportunities(round((1 + $variation) * $myReport->getTotalOpportunities()));
    $partnerReport->setImpressions(round((1 + $variation * $isHigherVariation) * $myReport->getImpressions()));

    $partnerReport->setPassbacks($partnerReport->getTotalOpportunities() - $partnerReport->getImpressions());
    if ($revenue === null) {
        $cpm = mt_rand($unifiedReportMinCpm, $unifiedReportMaxCpm);
        $partnerReport->setEstCpm($cpm / 100);
        $partnerReport->setEstRevenue($partnerReport->getEstCpm() * $partnerReport->getImpressions()/ 1000);
    } else {
        $partnerReport->setEstRevenue($revenue);
        if ($partnerReport->getImpressions() <= 0) {
            $partnerReport->setEstCpm(0);
        } else {
            $partnerReport->setEstCpm(1000 * $revenue / $partnerReport->getImpressions());
        }
    }

    $fillRate = $partnerReport->getTotalOpportunities() > 0 ? $partnerReport->getImpressions() / $partnerReport->getTotalOpportunities() : 0;
    $partnerReport->forceSetFillRate($fillRate);
}

/**
 * @param \Tagcade\Model\Core\AdNetworkPartnerInterface $partner
 * @param PublisherInterface $publisher
 * @param $partnerTagId
 * @return \Tagcade\Model\User\Role\SubPublisherInterface|null
 */
function getSubPublisherFromPartnerTagId(\Tagcade\Model\Core\AdNetworkPartnerInterface $partner, PublisherInterface $publisher, $partnerTagId)
{
    global $adTagRepository;

    $foundAdTags = $adTagRepository->getAdTagsForPartner($partner, $publisher, $partnerTagId);

    $subPublisherThatOwnsTags = null;
    foreach ($foundAdTags as $adTag) {
        /**
         * @var \Tagcade\Model\Core\AdTagInterface $adTag
         */
        $site = $adTag->getAdSlot()->getSite();
        $domain = $site->getDomain();
        $subPublisher = $site->getSubPublisher();
        $subPublisherId = $subPublisher instanceof \Tagcade\Model\User\Role\SubPublisherInterface ? $subPublisher->getId() : null;

        if ($subPublisherId != null) {

            if ($subPublisherThatOwnsTags instanceof \Tagcade\Model\User\Role\SubPublisherInterface && $subPublisherThatOwnsTags->getId() != $subPublisherId) {
                return null;
            }

            $subPublisherThatOwnsTags = $subPublisher;
        }
    }

    return $subPublisherThatOwnsTags;

}

function getDomainFromPartnerTagId(\Tagcade\Model\Core\AdNetworkPartnerInterface $partner, PublisherInterface $publisher, $partnerTagId)
{
    global $adTagRepository;

    $foundAdTags = $adTagRepository->getAdTagsForPartner($partner, $publisher, $partnerTagId);
    $adTag = current($foundAdTags);
    if (!$adTag instanceof \Tagcade\Model\Core\AdTagInterface || count($foundAdTags) > 1) {
        return null;
    }

    return $adTag->getAdSlot()->getSite()->getDomain();
}

function getRevenueConfig(\Tagcade\Model\User\Role\SubPublisherInterface $subPublisher, \Tagcade\Model\Core\AdNetworkInterface $adNetwork) {
    $revenueConfigs = $subPublisher->getSubPublisherPartnerRevenue();

    foreach ($revenueConfigs as $revenueConfig) {
        /**
         * @var \Tagcade\Model\Core\SubPublisherPartnerRevenueInterface $revenueConfig
         */

        if ($revenueConfig->getAdNetworkPartner()->getId() == $adNetwork->getNetworkPartner()->getId()) {
            return $revenueConfig;
        }
    }

    $revenueConfig = new \Tagcade\Entity\Core\SubPublisherPartnerRevenue();
    $revenueConfig->setRevenueOption(0);
    $revenueConfig->setRevenueValue(0);

    return $revenueConfig;
}

function createNetworkDomainAdTagReports(\Tagcade\Model\User\Role\PublisherInterface $publisher, $variation, DateTime $date, array &$networkAdTagRevenues, array &$networkDomainRevenues) {
    global $reportPartnerDomainAdTagBuilder, $em;
    $allAdNetworkDomainAdTagReports = $reportPartnerDomainAdTagBuilder->getAllReportsFor($publisher, $date, $date);

    if (!is_array($allAdNetworkDomainAdTagReports) || count($allAdNetworkDomainAdTagReports) < 1) {
        throw new Exception(sprintf('Not found associated Performance reports for partner of that publisher %d from date %s to date %s.
            Please run tool to create_performance_report_for_partner before this action.'
            , $publisher->getId(), $date->format('Y-m-d'), $date->format('Y-m-d')));
    }

    /**
     * @var Tagcade\Entity\Report\PerformanceReport\Display\Partner\AdNetworkDomainAdTagReport $adNetworkDomainAdTagReport
     */
    foreach($allAdNetworkDomainAdTagReports as $adNetworkDomainAdTagReport) {
        $partnerTagId = $adNetworkDomainAdTagReport->getPartnerTagId();
        $adNetwork = $adNetworkDomainAdTagReport->getAdNetwork();
        $domain = $adNetworkDomainAdTagReport->getDomain();

        $unifiedAdNetworkDomainAdTagReport = new Tagcade\Entity\Report\UnifiedReport\Network\NetworkDomainAdTagReport();
        $unifiedAdNetworkDomainAdTagReport->setAdNetwork($adNetwork);
        $unifiedAdNetworkDomainAdTagReport->setPartnerTagId($partnerTagId);
        $unifiedAdNetworkDomainAdTagReport->setDomain($domain);
        $unifiedAdNetworkDomainAdTagReport->setDate($date);
        $unifiedAdNetworkDomainAdTagReport->setName($adNetworkDomainAdTagReport->getName());

        setPartnerReport($unifiedAdNetworkDomainAdTagReport, $adNetworkDomainAdTagReport, $variation);

        $em->persist($unifiedAdNetworkDomainAdTagReport);

        if (!isset($networkAdTagRevenues[$adNetwork->getPublisherId()][$adNetwork->getId()][$partnerTagId])) {
            $networkAdTagRevenues[$adNetwork->getPublisherId()][$adNetwork->getId()][$partnerTagId] = $unifiedAdNetworkDomainAdTagReport->getEstRevenue();
        } else {
            $networkAdTagRevenues[$adNetwork->getPublisherId()][$adNetwork->getId()][$partnerTagId] += $unifiedAdNetworkDomainAdTagReport->getEstRevenue();
        }

        if (!isset($networkDomainRevenues[$adNetwork->getPublisherId()][$adNetwork->getId()][$domain])) {
            $networkDomainRevenues[$adNetwork->getPublisherId()][$adNetwork->getId()][$domain] = $unifiedAdNetworkDomainAdTagReport->getEstRevenue();
        } else {
            $networkDomainRevenues[$adNetwork->getPublisherId()][$adNetwork->getId()][$domain] += $unifiedAdNetworkDomainAdTagReport->getEstRevenue();
        }

        $subPublisher = getSubPublisherFromPartnerTagId($adNetwork->getNetworkPartner(), $adNetwork->getPublisher(), $partnerTagId);
        if ($subPublisher == null) {
            continue;
        }

        createNetworkDomainAdTagSubPublisherReports($subPublisher, $unifiedAdNetworkDomainAdTagReport);
    }
}

function createNetworkDomainAdTagSubPublisherReports(\Tagcade\Model\User\Role\SubPublisherInterface $subPublisher, Tagcade\Model\Report\UnifiedReport\Network\NetworkDomainAdTagReport $unifiedNetworkDomainAdTagReport)
{
    global $em;

    $unifiedNetworkDomainAdTagSubPublisherReport = new Tagcade\Entity\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReport();
    $unifiedNetworkDomainAdTagSubPublisherReport->setSubPublisher($subPublisher);
    $unifiedNetworkDomainAdTagSubPublisherReport->setAdNetwork($unifiedNetworkDomainAdTagReport->getAdNetwork());
    $unifiedNetworkDomainAdTagSubPublisherReport->setPartnerTagId($unifiedNetworkDomainAdTagReport->getPartnerTagId());
    $unifiedNetworkDomainAdTagSubPublisherReport->setDomain($unifiedNetworkDomainAdTagReport->getDomain());

    $config = getRevenueConfig($subPublisher, $unifiedNetworkDomainAdTagReport->getAdNetwork());
    setPartnerReportForSubPublisher($unifiedNetworkDomainAdTagSubPublisherReport, $unifiedNetworkDomainAdTagReport, $subPublisher, $config);

    $em->persist($unifiedNetworkDomainAdTagSubPublisherReport);
    $em->flush();
}

function createNetworkAdTagReports(\Tagcade\Model\User\Role\PublisherInterface $publisher, $variation, DateTime $date, array $revenues)
{

    global $reportPartnerAdTagBuilder, $em;

    $allAdNetworkAdTagReports = $reportPartnerAdTagBuilder->getAllReportsFor($publisher, $date, $date);

    if (!is_array($allAdNetworkAdTagReports) || count($allAdNetworkAdTagReports) < 1) {
        throw new Exception(sprintf('Not found associated Performance reports for partner of that publisher %d from date %s to date %s.
            Please run tool to create_performance_report_for_partner before this action.'
            , $publisher->getId(), $date->format('Y-m-d'), $date->format('Y-m-d')));
    }

    /**
     * @var Tagcade\Entity\Report\PerformanceReport\Display\Partner\AdNetworkAdTagReport $adNetworkAdTagReport
     */
    foreach($allAdNetworkAdTagReports as $adNetworkAdTagReport) {

        $partnerTagId = $adNetworkAdTagReport->getPartnerTagId();
        $adNetwork = $adNetworkAdTagReport->getAdNetwork();

        $unifiedAdNetworkAdTagReport = new Tagcade\Entity\Report\UnifiedReport\Network\NetworkAdTagReport();
        $unifiedAdNetworkAdTagReport->setAdNetwork($adNetwork);
        $unifiedAdNetworkAdTagReport->setPartnerTagId($partnerTagId);
        $unifiedAdNetworkAdTagReport->setDate($date);
        $unifiedAdNetworkAdTagReport->setName($adNetworkAdTagReport->getName());

        if (!isset($revenues[$publisher->getId()][$adNetwork->getId()][$partnerTagId])) {
            continue;
        }

        setPartnerReport($unifiedAdNetworkAdTagReport, $adNetworkAdTagReport, $variation, $revenues[$publisher->getId()][$adNetwork->getId()][$partnerTagId]);

        $em->persist($unifiedAdNetworkAdTagReport);

        $subPublisher = getSubPublisherFromPartnerTagId($adNetwork->getNetworkPartner(), $adNetwork->getPublisher(), $partnerTagId);
        if ($subPublisher == null) {
            continue;
        }

        createNetworkAdTagSubPublisherReports($subPublisher, $unifiedAdNetworkAdTagReport);
    }
}

function createNetworkAdTagSubPublisherReports(\Tagcade\Model\User\Role\SubPublisherInterface $subPublisher, Tagcade\Model\Report\UnifiedReport\Network\NetworkAdTagReport $unifiedAdNetworkAdTagReport)
{
    global $adTagRepository, $em;

    $unifiedAdNetworkAdTagSubPublisherReport = new Tagcade\Entity\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReport();
    $unifiedAdNetworkAdTagSubPublisherReport->setSubPublisher($subPublisher);
    $unifiedAdNetworkAdTagSubPublisherReport->setAdNetwork($unifiedAdNetworkAdTagReport->getAdNetwork());
    $unifiedAdNetworkAdTagSubPublisherReport->setPartnerTagId($unifiedAdNetworkAdTagReport->getPartnerTagId());

    $config = getRevenueConfig($subPublisher, $unifiedAdNetworkAdTagReport->getAdNetwork());
    setPartnerReportForSubPublisher($unifiedAdNetworkAdTagSubPublisherReport, $unifiedAdNetworkAdTagReport, $subPublisher, $config);

    $em->persist($unifiedAdNetworkAdTagSubPublisherReport);
    $em->flush();
}

function createNetworkSiteReports(\Tagcade\Model\User\Role\PublisherInterface  $publisher, $variation, DateTime $date, array $revenue)
{
    global $reportPartnerDomainBuilder, $em;

    $allAdNetworkDomainReports = $reportPartnerDomainBuilder->getSiteReportForAllPublisher($publisher, $date, $date);

    if (!is_array($allAdNetworkDomainReports) || count($allAdNetworkDomainReports) < 1) {
        throw new Exception(sprintf('Not found associated Performance reports for partner of that publisher %d from date %s to date %s.
            Please run tool to create_performance_report_for_partner before this action.'
            , $publisher->getId(), $date->format('Y-m-d'), $date->format('Y-m-d')));
    }

    /**
     * @var Tagcade\Entity\Report\PerformanceReport\Display\Partner\AdNetworkDomainReport $adNetworkDomainReport
     */
    foreach($allAdNetworkDomainReports as $adNetworkDomainReport) {
        $unifiedAdNetworkDomainReport = new Tagcade\Entity\Report\UnifiedReport\Network\NetworkSiteReport();
        $unifiedAdNetworkDomainReport->setAdNetwork($adNetworkDomainReport->getAdNetwork());
        $unifiedAdNetworkDomainReport->setDomain($adNetworkDomainReport->getDomain());
        $unifiedAdNetworkDomainReport->setDate($date);
        $unifiedAdNetworkDomainReport->setName($adNetworkDomainReport->getName());

        if (!isset($revenue[$publisher->getId()][$adNetworkDomainReport->getAdNetwork()->getId()][$adNetworkDomainReport->getDomain()])) {
            continue;
        }

        setPartnerReport($unifiedAdNetworkDomainReport, $adNetworkDomainReport, $variation, $revenue[$publisher->getId()][$adNetworkDomainReport->getAdNetwork()->getId()][$adNetworkDomainReport->getDomain()]);

        createNetworkSiteSubPublisherReports($unifiedAdNetworkDomainReport);

        $em->persist($unifiedAdNetworkDomainReport);
    }
}

function createNetworkSiteSubPublisherReports(Tagcade\Model\Report\UnifiedReport\Network\NetworkSiteReport $unifiedAdNetworkDomainReport) {
    global $siteRepository, $em;

    /**
     * @var Tagcade\Model\Core\SiteInterface[] $sites
     */
    $sites = $siteRepository->getSitesByDomain($unifiedAdNetworkDomainReport->getDomain());

    $subPublisher = null;
    if(count($sites) > 0) {
        $subPublisher = $sites[0]->getSubPublisher();

        foreach($sites as $site) {
            if(empty($site->getSubPublisher()) || $subPublisher->getId() != $site->getSubPublisher()->getId()) {
                $subPublisher = null;
                break;
            }
        }
    }

    if(!$subPublisher instanceof \Tagcade\Model\User\Role\SubPublisherInterface) {
        return;
    }

    $unifiedAdNetworkSiteSubPublisherReport = new Tagcade\Entity\Report\UnifiedReport\Network\NetworkSiteSubPublisherReport();
    $unifiedAdNetworkSiteSubPublisherReport->setSubPublisher($subPublisher);
    $unifiedAdNetworkSiteSubPublisherReport->setAdNetwork($unifiedAdNetworkDomainReport->getAdNetwork());
    $unifiedAdNetworkSiteSubPublisherReport->setDomain($unifiedAdNetworkDomainReport->getDomain());

    $config = getRevenueConfig($subPublisher, $unifiedAdNetworkDomainReport->getAdNetwork());
    setPartnerReportForSubPublisher($unifiedAdNetworkSiteSubPublisherReport, $unifiedAdNetworkDomainReport, $subPublisher, $config);

    $em->persist($unifiedAdNetworkSiteSubPublisherReport);
}

function createAdNetworkReports(\Tagcade\Model\User\Role\PublisherInterface  $publisher, $variation, DateTime $date, $revenues)
{
    global $reportPartnerAdNetworkRepository, $adNetworkDomainManager, $em;

    $allNetworks = $adNetworkDomainManager->getAdNetworksThatHavePartnerForPublisher($publisher);
    foreach($allNetworks as $adNetwork) {
        /**
         * @var \Tagcade\Model\Core\AdNetworkInterface $adNetwork
         */

        $tcNetworkReport = $reportPartnerAdNetworkRepository->getReportFor($adNetwork, $date, $date, $oneOrNull = true);
        if ($tcNetworkReport == null) {
            continue;
        }
        /**
         * @var \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReport $tcNetworkReport
         */

        $unifiedNetworkReport = new \Tagcade\Entity\Report\UnifiedReport\Network\NetworkReport();
        $unifiedNetworkReport->setAdNetwork($adNetwork);
        $unifiedNetworkReport->setDate($date);
        $unifiedNetworkReport->setName($tcNetworkReport->getName());
        if (!isset($revenues[$publisher->getId()][$adNetwork->getId()])) {
            continue;
        }
        $networkRevenue = 0;
        foreach($revenues[$publisher->getId()][$adNetwork->getId()] as $domain => $revenue) {
            $networkRevenue += $revenue;
        }

        setPartnerReport($unifiedNetworkReport, $tcNetworkReport, $variation, $networkRevenue);
        $em->persist($unifiedNetworkReport);
    }
}

function createNetworkAccountReports(\Tagcade\Model\User\Role\PublisherInterface  $publisher, $variation, DateTime $date, $revenues) {
    global $reportPartnerAccountBuilder, $em;

    $allAdNetworkAccountReports = $reportPartnerAccountBuilder->getReportFor($publisher, $date, $date);

    if (!is_array($allAdNetworkAccountReports) || count($allAdNetworkAccountReports) < 1) {
        throw new Exception(sprintf('Not found associated Performance reports for partner of that publisher #%d from date %s to date %s.\n
            Please run tool to create_performance_report_for_partner before this action.\n'
            , $publisher->getId(), $date->format('Y-m-d'), $date->format('Y-m-d')));
    }

    /**
     * @var Tagcade\Entity\Report\PerformanceReport\Display\Partner\AccountReport $adNetworkAccountReport
     */
    foreach($allAdNetworkAccountReports as $adNetworkAccountReport) {
        $unifiedAdNetworkDomainReport = new Tagcade\Entity\Report\UnifiedReport\Publisher\PublisherReport();
        $unifiedAdNetworkDomainReport->setPublisher($publisher);
        $unifiedAdNetworkDomainReport->setDate($date);
        $unifiedAdNetworkDomainReport->setName($adNetworkAccountReport->getName());

        if (!isset($revenues[$publisher->getId()])) {
            continue;
        }

        $accountRevenue = 0;
        foreach($revenues[$publisher->getId()] as $adNetworkId => $networkRevenue) {
            foreach($networkRevenue as $domain => $revenue) {
                $accountRevenue += $revenue;
            }
        }

        setPartnerReport($unifiedAdNetworkDomainReport, $adNetworkAccountReport, $variation, $accountRevenue);

        $em->persist($unifiedAdNetworkDomainReport);
    }
}

function aggregatePartnerReport(\Tagcade\Model\Report\PartnerReportInterface $partnerReport, \Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface $reportToAdd)
{
    $partnerReport->setImpressions($partnerReport->getImpressions() + $reportToAdd->getImpressions());
    $partnerReport->setEstRevenue($partnerReport->getEstRevenue() + $reportToAdd->getEstRevenue());

    $partnerReport->setPassbacks($partnerReport->getPassbacks() + $reportToAdd->getPassbacks());
    $partnerReport->setTotalOpportunities($partnerReport->getTotalOpportunities() + $reportToAdd->getTotalOpportunities());
    $partnerReport->setFillRate();
    $partnerReport->calculateEstCpm();
}

function createUnifiedSubPublisherAndNetworkReport(PublisherInterface $publisher, $variation, DateTime $date)
{
    global $subPublisherManager, $adNetworkDomainManager, $reportAdTagAdNetworkSubPublisherRepository, $em, $adTagRepository;
    // Step 0. Get all sub publishers then loop over
    $subPublishers = $subPublisherManager->allForPublisher($publisher);

    /**
     * @var \Tagcade\Model\User\Role\SubPublisherInterface $subPublisher
     */
    foreach ($subPublishers as $subPublisher) {
        // Step 1. Get all networks which is partner for current sub publisher
        $allNetworks = $adNetworkDomainManager->getAdNetworksThatHavePartnerForSubPublisher($subPublisher);
        $subPublisherReport = new \Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherReport();
        $subPublisherReport->setSubPublisher($subPublisher);
        $subPublisherReport->setDate($date);
        $subPublisherReport->setName($subPublisher->getUser()->getUsername());

        /**
         * @var \Tagcade\Model\Core\AdNetworkInterface $adNetwork
         */
        foreach ($allNetworks as $adNetwork) {
            /**
             * @var \Tagcade\Model\Core\AdTagInterface[] $adTags
             */
            $adTags = $adTagRepository->getAdTagsThatHavePartnerForAdNetworkWithSubPublisher($adNetwork, $subPublisher);

            echo sprintf('date %s performance report sub publisher %d, network %d' . "\n", $date->format('Y-m-d'), $subPublisher->getId(), $adNetwork->getId());

            $subPublisherNetworkUnifiedReport = new \Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherNetworkReport;
            $subPublisherNetworkUnifiedReport->setSubPublisher($subPublisher);
            $subPublisherNetworkUnifiedReport->setAdNetwork($adNetwork);
            $subPublisherNetworkUnifiedReport->setName($adNetwork->getName());
            $subPublisherNetworkUnifiedReport->setDate($date);

            foreach($adTags as $adTag) {
                // get all adtag report for the sub publisher
                $subPublisherAdTagReport = $reportAdTagAdNetworkSubPublisherRepository->getReportFor($adNetwork, $adTag->getPartnerTagId(), $subPublisher, $date, $date, $oneOrNull = true);

                if(empty($subPublisherAdTagReport)) {
                    continue;
                }

                aggregatePartnerReport($subPublisherNetworkUnifiedReport, $subPublisherAdTagReport);
            }

            if(empty($subPublisherNetworkUnifiedReport->getTotalOpportunities())) {
                continue;
            }

            $em->persist($subPublisherNetworkUnifiedReport);

            // Step 5. aggregate to have unified sub publisher report
            aggregatePartnerReport($subPublisherReport, $subPublisherNetworkUnifiedReport);
        }

        if(empty($subPublisherReport->getTotalOpportunities())) {
            continue;
        }

        $em->persist($subPublisherReport);
    }
}

function setPartnerReportForSubPublisher(Tagcade\Model\Report\UnifiedReport\ReportInterface $unifiedSubPublisherReport, Tagcade\Model\Report\PerformanceReport\Display\ReportInterface $unifiedReport, \Tagcade\Model\User\Role\SubPublisherInterface $subPublisher, \Tagcade\Model\Core\SubPublisherPartnerRevenueInterface $revenueConfig) {
    $unifiedSubPublisherReport->setDate($unifiedReport->getDate());
    $unifiedSubPublisherReport->setName($unifiedReport->getName());
    $unifiedSubPublisherReport->setTotalOpportunities($unifiedReport->getTotalOpportunities());
    $unifiedSubPublisherReport->setImpressions($unifiedReport->getImpressions());
    $unifiedSubPublisherReport->setPassbacks($unifiedReport->getPassbacks());


    $cpm = $unifiedReport->getEstCpm();
    $valueConfig = $revenueConfig;
    /**
     * @var \Tagcade\Entity\Core\SubPublisherPartnerRevenue[] $valueConfigs
     */
//    if(count($subPublisher->getSubPublisherPartnerRevenue()) > 0) {
//        $valueConfig = $subPublisher->getSubPublisherPartnerRevenue()[0];

        if($valueConfig->getRevenueOption() == Tagcade\Model\Core\SubPublisherPartnerRevenue::REVENUE_OPTION_CPM_PERCENT) {
            $cpm = $unifiedReport->getEstCpm() * $valueConfig->getRevenueValue() / 100;
        } elseif($valueConfig->getRevenueOption() == Tagcade\Model\Core\SubPublisherPartnerRevenue::REVENUE_OPTION_CPM_FIXED) {
            $cpm = $valueConfig->getRevenueValue();
        }
//    }

    $unifiedSubPublisherReport->setEstCpm($cpm);
    $revenue = $cpm * $unifiedSubPublisherReport->getImpressions() / 1000;
    $unifiedSubPublisherReport->setEstRevenue($revenue);
    $unifiedSubPublisherReport->forceSetFillRate($unifiedReport->getFillRate());
}

foreach($dateRange as $date) {
    /**
     * @var \DateTime $date
     */
    echo sprintf("%s processing... @ %s\n", $date->format('Y-m-d'), date('c'));
    foreach ($allPublishers as $publisher) {
        $variation = mt_rand(0, $unifiedReportMaxVariation) / 100;

        $networkAdTagRevenues = [];
        $networkDomainRevenues = [];

        createNetworkDomainAdTagReports($publisher, $variation, $date, $networkAdTagRevenues, $networkDomainRevenues);

        createNetworkAdTagReports($publisher, $variation, $date, $networkAdTagRevenues);

        createNetworkSiteReports($publisher, $variation, $date, $networkDomainRevenues);

        createAdNetworkReports($publisher, $variation, $date, $networkDomainRevenues);

        createNetworkAccountReports($publisher, $variation, $date, $networkDomainRevenues);

        createUnifiedSubPublisherAndNetworkReport($publisher, $variation, $date);
    }

    $em->flush();

    echo sprintf("%s created @ %s\n", $date->format('Y-m-d'), date('c'));

    gc_collect_cycles();
}
//
//// update comparison reports
//$reportComparisonCreator->updateComparisonForPublisher($publisher, $begin, $end);
