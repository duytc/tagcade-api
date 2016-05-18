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
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReport as SiteReportPlatform;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReport as SiteReportNetwork;
use Tagcade\Service\Report\PerformanceReport\Display\EstCpmCalculatorInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdTagReport as AdTagPlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReport as AdSlotPlatformReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReport as AdTagNetworkReport;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Bundle\UserBundle\Entity\User as AbstractUser;

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
 * @var \Tagcade\DomainManager\SiteManagerInterface $siteManager
 */
$siteManager = $container->get('tagcade.domain_manager.site');

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

$ronSlotId = RON_SLOT_ID; // id of ron ad slot that have linked ad slots in duplicated sites
$siteTobeKept = KEEP_SITE_ID; // id of site to be kept
$sitesTobeRemoved = explode(',', REMOVE_SITES); // ids of sites to be removed
$sitesTobeRemoved = array_map(function($st) { return (int)$st; }, $sitesTobeRemoved);

$ronAdSlot = $ronAdSlotManager->find($ronSlotId);
if (!$ronAdSlot instanceof RonAdSlotInterface) {
    throw new RuntimeException('Expect id of existing ron ad slot');
}

function mergeAdTagPlatformReport(AdTagInterface $adTag, AdTagPlatformReport $keep, AdTagPlatformReport $merge, EstCpmCalculatorInterface $cpmCalculator) {
    /**
     * @var AdTagInterface $adTag
     */
    $keep->setTotalOpportunities($keep->getTotalOpportunities() + $merge->getTotalOpportunities());
    $keep->setImpressions($keep->getImpressions() + $merge->getImpressions());
    $keep->setFirstOpportunities($keep->getFirstOpportunities() + $merge->getFirstOpportunities());
    $keep->setVerifiedImpressions($keep->getVerifiedImpressions() + $merge->getVerifiedImpressions());
    $keep->setEstCpm($cpmCalculator->getEstCpmForAdTag($adTag, $keep->getDate()));

    if (!$adTag->getAdSlot() instanceof \Tagcade\Model\Core\NativeAdSlotInterface) {
        $keep->setPassbacks($keep->getPassbacks() + $merge->getPassbacks());
        $keep->setUnverifiedImpressions($keep->getUnverifiedImpressions() + $merge->getUnverifiedImpressions());
        $keep->setBlankImpressions($keep->getBlankImpressions() + $merge->getBlankImpressions());
        $keep->setVoidImpressions($keep->getVoidImpressions() + $merge->getVoidImpressions());
        $keep->setClicks($keep->getClicks() + $merge->getClicks());
    }
};

function mergeAdTagNetworkReport(AdTagInterface $adTag, AdTagNetworkReport $keep, AdTagNetworkReport $merge, EstCpmCalculatorInterface $cpmCalculator) {
    /**
     * @var AdTagInterface $adTag
     */
    $keep->setTotalOpportunities($keep->getTotalOpportunities() + $merge->getTotalOpportunities());
    $keep->setImpressions($keep->getImpressions() + $merge->getImpressions());
    $keep->setFirstOpportunities($keep->getFirstOpportunities() + $merge->getFirstOpportunities());
    $keep->setVerifiedImpressions($keep->getVerifiedImpressions() + $merge->getVerifiedImpressions());
    $keep->setEstCpm($cpmCalculator->getEstCpmForAdTag($adTag, $keep->getDate()));

    if (!$adTag->getAdSlot() instanceof \Tagcade\Model\Core\NativeAdSlotInterface) {
        $keep->setPassbacks($keep->getPassbacks() + $merge->getPassbacks());
        $keep->setUnverifiedImpressions($keep->getUnverifiedImpressions() + $merge->getUnverifiedImpressions());
        $keep->setBlankImpressions($keep->getBlankImpressions() + $merge->getBlankImpressions());
        $keep->setVoidImpressions($keep->getVoidImpressions() + $merge->getVoidImpressions());
        $keep->setClicks($keep->getClicks() + $merge->getClicks());
    }
}

function mergeAdSlotPlatformReport(AdSlotPlatformReport $keep, AdSlotPlatformReport $merge, BillingCalculatorInterface $billingCalculator) {
    /**
     * @var BillingCalculatorInterface $billingCalculator
     */

    $adSlot = $keep->getAdSlot();

    $keep->setSlotOpportunities($keep->getSlotOpportunities() + $merge->getSlotOpportunities());
    $rateAmount = $billingCalculator->calculateTodayBilledAmountForPublisher($adSlot->getSite()->getPublisher(), AbstractUser::MODULE_DISPLAY, $keep->getSlotOpportunities());
    if ($rateAmount->getRate()->isCustom()) {
        $keep->setCustomRate($rateAmount->getRate()->getCpmRate());
    }
}
// Get all ad slots, ad tags to be removed and ad slots, ad tags to be kept
// also extract affected networks

$allAdSlots = $ronAdSlot->getLibraryAdSlot()->getAdSlots()->toArray();
$keepAdSlots = [];
$removeAdSlots = [];
$keepAdTags = [];
$affectedAdNetworks = [];
$removeAdTags = [];
foreach ($allAdSlots as $adSlot) {
    /**
     * @var BaseAdSlotInterface $adSlot
     */
    if ($adSlot->getSite()->getId() == $siteTobeKept) {
        if (!in_array($adSlot, $keepAdSlots)) {
            $keepAdSlots[] = $adSlot;
            foreach ($adSlot->getAdTags() as $adTag) {
                /**
                 * @var AdTagInterface $adTag
                 */
                if (!in_array($adTag, $keepAdTags)) {
                    $keepAdTags[] = $adTag;
                }

                if (!in_array($adTag->getAdNetwork(), $affectedAdNetworks)) {
                    $affectedAdNetworks[] = $adTag->getAdNetwork();
                }
            }

        }
    }
    else {
        if (!in_array($adSlot, $removeAdSlots)) {
            $removeAdSlots[] = $adSlot;
            foreach ($adSlot->getAdTags() as $adTag) {
                if (!in_array($adTag, $removeAdTags)) {
                    $removeAdTags[] = $adTag;
                }
            }
        }
    }
}

/**
 * @var \Tagcade\Model\Core\SiteInterface $keepSite
 */
$keepSite = $siteManager->find($siteTobeKept);

foreach($dateRange as $date) {
    /**
     * @var \DateTime $date
     */
    echo sprintf("merging for date %s...\n", $date->format('Y-m-d'));
    // Step 1. merge ad tags for keepAdTags list
    foreach ($keepAdTags as $adTag) {
        /**
         * @var AdTagInterface $adTag
         */
        //1. Get siblings of the ad tags
        $removeTagSiblings = array_filter(
            $removeAdTags,
            function(AdTagInterface $at) use ($adTag)
            {
                return $at->getLibraryAdTag()->getId() === $adTag->getLibraryAdTag()->getId();
            }
        );

        //2. Get report of these siblings // network and performance hierarchy
        $currentAdTagPlatformReport = $adTagReportRepositoryPlatform->getReportFor($adTag, $date, $date);
        if (count($currentAdTagPlatformReport) > 1) {
            throw new RuntimeException('Invalid date range. Expect report for one day only');
        }

        $currentAdTagPlatformReport = current($currentAdTagPlatformReport);

        $currentAdTagNetworkReport = $adTagReportRepositoryNetwork->getReportFor($adTag, $date, $date);
        $currentAdTagNetworkReport = current($currentAdTagNetworkReport);

        foreach ($removeTagSiblings as $sib) {
            $mergeReport = $adTagReportRepositoryPlatform->getReportFor($sib, $date, $date);
            if (count($mergeReport) > 1) {
                throw new RuntimeException('Invalid date range. Expect ad tag report for one day only ');
            }
            $mergeReport = current($mergeReport);
            if ($currentAdTagPlatformReport instanceof AdTagPlatformReport && $mergeReport instanceof AdTagPlatformReport) {
                //3. Does merge
                mergeAdTagPlatformReport($adTag, $currentAdTagPlatformReport, $mergeReport, $cpmCalculator);
            }
            else {
                echo sprintf("cannot merge ad tag platform report for ad tag %d and its sibiling %d on date %s ... \n", $adTag->getId(), $sib->getId(), $date->format('Y-m-d'));
            }


            $mergeAdTagNetwork = $adTagReportRepositoryNetwork->getReportFor($sib, $date, $date);
            $mergeAdTagNetwork = current($mergeAdTagNetwork);
            if ($currentAdTagNetworkReport instanceof AdTagNetworkReport && $mergeAdTagNetwork instanceof AdTagNetworkReport) {
                // cannot merge these reports
                mergeAdTagNetworkReport($adTag, $currentAdTagNetworkReport, $mergeAdTagNetwork, $cpmCalculator);
            }
            else {
                echo sprintf("cannot merge ad tag report network hierarchy for ad tag %d and its sibiling %d on date %s ... \n", $adTag->getId(), $sib->getId(), $date->format('Y-m-d'));
            }
        }

        if ($currentAdTagPlatformReport instanceof AdTagPlatformReport) {
            $em->persist($currentAdTagPlatformReport);
        }

        if ($currentAdTagNetworkReport instanceof AdTagNetworkReport) {
            $em->persist($currentAdTagNetworkReport);
        }
    }

    $em->flush(); // this will make sure all ad tags report count is updated first

    // merge ad slots
    foreach ($keepAdSlots as $adSlot) {
        $removeSlotSiblings = array_filter(
            $removeAdSlots,
            function(BaseAdSlotInterface $sl) use ($adSlot) {
                return $sl->getLibraryAdSlot()->getId() == $adSlot->getLibraryAdSlot()->getId();
            }
        );

        $currentAdSlotReportPlatform = $adSlotReportRepositoryPlatform->getReportFor($adSlot, $date, $date);
        if (count($currentAdSlotReportPlatform) > 1) {
            throw new RuntimeException('Invalid date range. Expect slot report for one day only');
        }

        $currentAdSlotReportPlatform = current($currentAdSlotReportPlatform);
        foreach ($removeSlotSiblings as $sl) {
            $mergeSlot = $adSlotReportRepositoryPlatform->getReportFor($sl, $date, $date);
            $mergeSlot = current($mergeSlot);
            if ($currentAdSlotReportPlatform instanceof AdSlotPlatformReport && $mergeSlot instanceof AdSlotPlatformReport) {
                // cannot merge these reports
                mergeAdSlotPlatformReport($currentAdSlotReportPlatform, $mergeSlot, $billingCalculator);
            }
            else {
                echo sprintf("cannot merge ad slot report platform hierarchy for ad slot %d and its sibiling %d on date %s ... \n", $adSlot->getId(), $sl->getId(), $date->format('Y-m-d'));
            }
        }

        if ($currentAdSlotReportPlatform instanceof AdSlotPlatformReport) {
            $em->persist($currentAdSlotReportPlatform);
        }
    }

    $em->flush(); // this will make sure all ad slot report count is updated first

    /**
     * @var SiteReportPlatform $keepSiteReport
     */
    $keepSiteReport = $siteReportRepositoryPlatform->getReportFor($keepSite, $date, $date);
    if (count($keepSiteReport) > 1) {
        throw new RuntimeException('Invalid date range. Expect site report for one day only ');
    }

    $keepSiteReport = current($keepSiteReport);
    if ($keepSiteReport instanceof SiteReportPlatform) {
        $keepSiteReport->setCalculatedFields(); // this will make sure to update sub reports
    }

    foreach ($affectedAdNetworks as $nw) {
        /**
         * @var SiteReportNetwork $keepSiteNetworkReport
         */
        $keepSiteNetworkReport = $siteReportRepositoryNetwork->getReportFor($keepSite, $nw, $date, $date);
        $keepSiteNetworkReport = current($keepSiteNetworkReport);
        if ($keepSiteNetworkReport instanceof SiteReportNetwork) {
            $keepSiteNetworkReport->setCalculatedFields();
        }
    }


    // optional: we can remove report of duplicated sites both network and platform hierarchy
    foreach ($sitesTobeRemoved as $removeSiteId) {
        $removeSite = $siteManager->find($removeSiteId);
        $removeReport = $siteReportRepositoryPlatform->getReportFor($removeSite, $date, $date);
        $removeReport = current($removeReport);
        if ($removeReport instanceof SiteReportPlatform) {
            $em->remove($removeReport);
        }

        foreach ($affectedAdNetworks as $nw) {
            $removeSiteNetworkReport = $siteReportRepositoryNetwork->getReportFor($removeSite, $nw, $date, $date);
            if ($removeReport instanceof SiteReportNetwork) {
                $em->remove($removeReport);
            }
        }
    }

    echo sprintf("merge finish for date %s... \n", $date->format('Y-m-d'));

}

$em->flush(); // last flush to store everything in db


