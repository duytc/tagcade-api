<?php


$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';
require_once __DIR__ . '/config.php';

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AdTagReportRepositoryInterface as AdTagPlatformReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportRepositoryInterface as AdSlotPlatformReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportRepositoryInterface as SitePlatformReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReportRepositoryInterface as AdTagNetworkReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportRepositoryInterface as SiteNetworkReportRepositoryInterface;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdTagReportInterface as AdTagPlatformReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReport as SiteReportPlatform;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReport as SiteReportNetwork;

use Tagcade\Service\Report\PerformanceReport\Display\EstCpmCalculatorInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdTagReport as AdTagPlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReport as AdSlotPlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReport as AdTagNetworkReport;

use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;


$env = getenv('SYMFONY_ENV') ?: 'prod';
$debug = false;

if ($env == 'dev') {
    $debug = true;
}

$kernel = new AppKernel($env, $debug);
$kernel->boot();

$container = $kernel->getContainer();

/**
 * @var \Doctrine\ORM\EntityManagerInterface $em
 */
$em = $container->get('doctrine.orm.entity_manager');

/**
 * @var \Tagcade\DomainManager\RonAdSlotManagerInterface $ronAdSlotManager
 */
$ronAdSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');
/**
 * @var \Tagcade\DomainManager\AdSlotManagerInterface $adSlotManager
 */
$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
/**
 * @var \Tagcade\DomainManager\SiteManagerInterface $siteManager
 */
$siteManager = $container->get('tagcade.domain_manager.site');
/**
 * @var \Tagcade\DomainManager\AdTagManagerInterface $adTagManager
 */
$adTagManager = $container->get('tagcade.domain_manager.ad_tag');

/**
 * @var AdTagPlatformReportRepositoryInterface $adTagReportRepositoryPlatform
 */
$adTagReportRepositoryPlatform = $container->get('tagcade.repository.report.performance_report.display.hierarchy.platform.ad_tag');

/**
 * @var AdTagNetworkReportRepositoryInterface $adTagReportRepositoryNetwork
 */
$adTagReportRepositoryNetwork = $container->get('tagcade.repository.report.performance_report.display.hierarchy.ad_network.ad_tag');
/**
 * @var AdSlotPlatformReportRepositoryInterface $adSlotReportRepositoryPlatform
 */
$adSlotReportRepositoryPlatform = $container->get('tagcade.repository.report.performance_report.display.hierarchy.platform.ad_slot');
/**
 * @var SitePlatformReportRepositoryInterface $siteReportRepositoryPlatform
 */
$siteReportRepositoryPlatform = $container->get('tagcade.repository.report.performance_report.display.hierarchy.platform.site');
/**
 * @var SiteNetworkReportRepositoryInterface $siteReportRepositoryNetwork
 */
$siteReportRepositoryNetwork = $container->get('tagcade.repository.report.performance_report.display.hierarchy.ad_network.site');

/**
 * @var \Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportRepositoryInterface $adNetworkReportRepository
 */
$adNetworkReportRepository = $container->get('tagcade.repository.report.performance_report.display.hierarchy.ad_network.ad_network');

/**
 * @var EstCpmCalculatorInterface $cpmCalculator
 */
$cpmCalculator = $container->get('tagcade.service.report.performance_report.est_cpm_calculator');
/**
 * @var BillingCalculatorInterface $billingCalculator
 */
$billingCalculator = $container->get('tagcade.service.report.performance_report.display.billing.billing_calculator');

$begin = new DateTime(START_DATE);
$end = new DateTime(END_DATE);
$end = $end->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($begin, $interval ,$end);

$moveSlotId = MOVE_AD_SLOT_ID; // id of ron ad slot that have linked ad slots in duplicated sites
$siteTobeKept = KEEP_SITE_ID; // id of site to be kept
$sitesTobeRemoved = explode(',', REMOVE_SITES); // ids of sites to be removed
$sitesTobeRemoved = array_map(function($st) { return (int)$st; }, $sitesTobeRemoved);

/**
 * @var BaseAdSlotInterface $moveAdSlot
 */
$moveAdSlot = $adSlotManager->find($moveSlotId);

if ($moveAdSlot->getSite()->getId() == $siteTobeKept) {
    throw new RuntimeException('Expect that the move ad slot not in remove site list');
}
/**
 * @var \Tagcade\Model\Core\SiteInterface $keepSite
 */
$keepSite = $siteManager->find($siteTobeKept);

// update for network hierarchy
$adTags = $adTagManager->getAdTagsForAdSlot($moveAdSlot);

foreach($dateRange as $date) {
    /**
     * @var \DateTime $date
     */
    echo sprintf("merging for date %s...\n", $date->format('Y-m-d'));

    $siteReport = $siteReportRepositoryPlatform->getReportFor($keepSite, $date, $date);
    $siteReport = current($siteReport);
    $moveAdSlotReports = $adSlotReportRepositoryPlatform->getReportFor($moveAdSlot, $date, $date);
    foreach ($moveAdSlotReports as $adSlotReport) {
        /**
         * @var \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface $adSlotReport
         */
        $adSlotReport->setSuperReport($siteReport);
    }


    foreach ($adTags as $adTag) {
        /**
         * @var AdTagInterface $adTag
         */
        /**
         * @var \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReport $adTagNetworkReport
         */
        $adTagNetworkReport = $adTagReportRepositoryNetwork->getReportFor($adTag, $date, $date);
        $adTagNetworkReport = current($adTagNetworkReport);

        $siteNetworkReport = $siteReportRepositoryNetwork->getReportFor($keepSite, $adTag->getAdNetwork(), $date, $date);
        $siteNetworkReport = current($siteNetworkReport);

        if (!$adTagNetworkReport instanceof \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReportInterface) {
            continue;
        }

        if(!$siteNetworkReport instanceof \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportInterface) {
            $adNetworkReports = $adNetworkReportRepository->getReportFor($adTag->getAdNetwork(), $date, $date);
            $adNetworkReport = current($adNetworkReports);

            $siteNetworkReport =  new \Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\SiteReport();
            $siteNetworkReport->setSuperReport($adNetworkReport);
            $siteNetworkReport->setSite($keepSite);
            $siteNetworkReport->setName($keepSite->getName());
            $siteNetworkReport->setDate($date);
            $siteNetworkReport->setTotalOpportunities(0);
            $siteNetworkReport->setImpressions(0);
            $siteNetworkReport->setPassbacks(0);
            $siteNetworkReport->setFillRate(0);
            $siteNetworkReport->setEstRevenue(0);
            $siteNetworkReport->setEstCpm(null);
            $siteNetworkReport->setFirstOpportunities(0);
            $siteNetworkReport->setVerifiedImpressions(0);
            $siteNetworkReport->setUnverifiedImpressions(0);
            $siteNetworkReport->setBlankImpressions(0);
            $siteNetworkReport->setVoidImpressions(0);
            $siteNetworkReport->setClicks(0);


            // update mapping sub and super reports
            foreach ($adTags as $at) {
                $atNetworkReport = $adTagReportRepositoryNetwork->getReportFor($at, $date, $date);
                $atNetworkReport = current($atNetworkReport);
                if (!$atNetworkReport instanceof \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReport) {
                    continue;
                }

                $atNetworkReport->setSuperReport($siteNetworkReport);
                $em->persist($atNetworkReport);

                $siteNetworkReport->addSubReport($atNetworkReport);
            }

            $em->persist($siteNetworkReport);
        }

        $adTagNetworkReport->setSuperReport($siteNetworkReport);
    }
}

$em->flush(); // make sure ad slot report has its super report is updated

foreach($dateRange as $date) {
    $siteReport = $siteReportRepositoryPlatform->getReportFor($keepSite, $date, $date);
    $siteReport = current($siteReport);

    $removeSiteReport = $siteReportRepositoryPlatform->getReportFor($moveAdSlot->getSite(), $date, $date);
    $removeSiteReport = current($removeSiteReport);
    if ($removeSiteReport instanceof \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface) {
        $removeSiteReport->setCalculatedFields();
    }
    /**
     * @var \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface $siteReport
     */
    if ($siteReport instanceof \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface) {
        $siteReport->setCalculatedFields();
    }


    foreach ($adTags as $adTag) {
        $siteNetworkReport = $siteReportRepositoryNetwork->getReportFor($keepSite, $adTag->getAdNetwork(), $date, $date);
        $siteNetworkReport = current($siteNetworkReport);
        if ($siteNetworkReport instanceof \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReport) {
            $siteNetworkReport->setCalculatedFields();
        }

        $removeSiteNetworkReport = $siteReportRepositoryNetwork->getReportFor($moveAdSlot->getSite(), $adTag->getAdNetwork(), $date, $date);
        $removeSiteNetworkReport = current($removeSiteNetworkReport);
        if ($removeSiteNetworkReport instanceof \Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReport) {
            $removeSiteNetworkReport->setCalculatedFields();
        }

    }
}

$moveAdSlot->setSite($keepSite); // move the moveAdSlot to keep site

$em->flush(); // update all site reports




