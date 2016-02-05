<?php


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\LibraryAdSlotManagerInterface;
use Tagcade\DomainManager\LibrarySlotTagManagerInterface;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\CalculateRevenueTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;

class SnapshotCreatorTest extends \Codeception\TestCase\Test
{
    use CalculateRatiosTrait;
    use CalculateWeightedValueTrait;
    use CalculateRevenueTrait;

    /** @var \UnitTester */
    protected $tester;
    /** @var EntityManagerInterface $em */
    protected $em;
    /** @var PublisherManagerInterface $publisherManager */
    protected $publisherManager;
    /** @var AdSlotManagerInterface */
    protected $adSlotManager;
    /** @var LibraryAdSlotManagerInterface */
    protected $libraryAdSlotManager;
    /** @var SiteManagerInterface */
    protected $siteManager;
    /** @var AdTagManagerInterface */
    protected $adTagManager;
    /** @var AdNetworkManagerInterface */
    protected $adNetworkManager;
    /** @var RonAdSlotManagerInterface */
    protected $ronAdSlotManager;
    /** @var LibrarySlotTagManagerInterface */
    protected $librarySlotTagManager;
    /** @var ReportBuilderInterface */
    protected $reportBuilder;
    /** @var EventCounterInterface */
    protected $testEventCounter;

    protected $publisher;
    protected $adNetwork;
    protected $site;

    /**
     * @var array
     */
    protected $libraryAdSlots;
    /** @var array */
    protected $adSlots;
    /** @var array */
    protected $adTags;
    /** @var array */
    protected $ronAdSlots;


    protected function _before()
    {
        $this->em = $this->tester->grabServiceFromContainer('doctrine.orm.entity_manager');
        $this->publisherManager = $this->tester->grabServiceFromContainer('tagcade_user.domain_manager.publisher');
        $this->adSlotManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.ad_slot');
        $this->siteManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.site');
        $this->adNetworkManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.ad_network');
        $this->adTagManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.ad_tag');
        $this->reportBuilder = $this->tester->grabServiceFromContainer('tagcade.service.report.performance_report.display.selector.report_builder');

        //create publisher
        $this->publisher = new Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User();
        $this->publisher
            ->setUsername('test_creator')
            ->setPlainPassword('123456')
            ->setEmail('test_creator@tagcade.com')
            ->setEnabled(true)
            ->setUuid(uniqid(''))
            ->setCompany('creator test'); // doesn't return $this so cannot chain

        $this->publisher->setEnabledModules([Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User::MODULE_DISPLAY]);

        $this->publisherManager->save($this->publisher);

        // create ad network
        $this->adNetwork = (new Tagcade\Entity\Core\AdNetwork())
            ->setName('Creator Test Ad Network')
            ->setPublisher($this->publisher);
        $this->em->persist($this->adNetwork);

        //create sites
        $this->site = (new Tagcade\Entity\Core\Site())
            ->setName('Creator Test Site')
            ->setDomain('site_creator.com')
            ->setAutoCreate(false)
            ->setEnableSourceReport(false)
            ->setPublisher($this->publisher)
            ->setAdSlots(new \Doctrine\Common\Collections\ArrayCollection())
        ;

        for($i=0;$i<10;$i++) {
            // create ad slot
            $adSlot = (new Tagcade\Entity\Core\DisplayAdSlot())
                ->setLibraryAdSlot(
                    (new Tagcade\Entity\Core\LibraryDisplayAdSlot())
                        ->setName("Display AdSlot " . $i++)
                        ->setType('display')
                        ->setVisible(false)
                        ->setPublisher($this->publisher)
                )
                ->setAutoFit(true)
                ->setPassbackMode('position')
                ->setHeight(200)
                ->setWidth(400)
                ->setSite($this->site);

            // create ad tag
            for($k=0;$k<3;$k++) {
                $adTag = (new Tagcade\Entity\Core\AdTag())
                    ->setLibraryAdTag(
                        (new Tagcade\Entity\Core\LibraryAdTag())->setName(sprintf('AdTag %d', $k) )
                            ->setVisible(false)
                            ->setHtml(sprintf('ad tag %d html', $k))
                            ->setAdType(0)
                            ->setAdNetwork($this->adNetwork)
                    )
                    ->setAdSlot($adSlot)
                    ->setActive(true)
                    ->setFrequencyCap(11)
                    ->setRefId(uniqid('', true));

                $this->em->persist($adTag);
                $this->adTags[] = $adTag;

                $adSlot->getAdTags()->add($adTag);
            }

            $this->em->persist($adSlot);
            $this->site->getAllAdSlots()->add($adSlot);
            $this->adSlots[] = $adSlot;
        }

        $this->em->persist($this->site);

        $this->em->flush();

        $this->testEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter($this->adSlots);
        $this->testEventCounter->refreshTestData();

        $redis = new RedisArray(['localhost']);
        $cache = new Tagcade\Cache\Legacy\Cache\RedisArrayCache();
        $cache->setRedis($redis);

        $cacheEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter($cache);
        $cacheEventCounter->setDate(new DateTime('today'));

        foreach($this->testEventCounter->getAdSlotData() as $slotId => $slotData) {
            $cache->save(
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY,
                    $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
                ),
                $slotData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_SLOT_OPPORTUNITY]
            );

            unset($slotId, $slotData);
        }

        foreach($this->testEventCounter->getAdTagData() as $tagId => $tagData) {
            $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_TAG, $tagId);

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_OPPORTUNITY, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_OPPORTUNITY]
            );

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_FIRST_OPPORTUNITY, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_FIRST_OPPORTUNITY]
            );

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_IMPRESSION, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_IMPRESSION]
            );

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_VERIFIED_IMPRESSION, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_VERIFIED_IMPRESSION]
            );

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_UNVERIFIED_IMPRESSION, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_UNVERIFIED_IMPRESSION]
            );

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_BLANK_IMPRESSION, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_BLANK_IMPRESSION]
            );

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_PASSBACK, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_PASSBACK]
            );

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_VOID_IMPRESSION, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_VOID_IMPRESSION]
            );

            $cache->save(
                $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_CLICK, $namespace),
                $tagData[\Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter::KEY_CLICK]
            );
            unset($tagId, $tagData);
        }
    }

    protected function _after()
    {
    }

    /**
     * @test
     */
    public function platformReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getPlatformReport($params);
        $this->tester->assertNotNull($report);

        $slotOpportunities = 0;
        /** @var BaseAdSlotInterface $adSlot */
        foreach($this->adSlots as $adSlot) {
            $slotOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
        }

        $impression = 0;
        $passBack = 0;
        $totalOpportunities = 0;
        foreach($this->adTags as $adTag) {
            $totalOpportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());

        }
        $fillRate = round($this->getRatio($impression, $slotOpportunities), 4);

        $this->tester->assertEquals($totalOpportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($slotOpportunities, $report->getSlotOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($fillRate, $report->getFillRate());

        $reportCount = count($report->getReports());
        $averageTotalOpportunities = $this->getRatio($totalOpportunities, $reportCount);
        $averageImpressions = $this->getRatio($impression, $reportCount);
        $averagePassbacks = $this->getRatio($passBack, $reportCount);
        $averageSlotOpportunities = $this->getRatio($slotOpportunities, $reportCount);

        $this->tester->assertEquals($averageTotalOpportunities, $report->getAverageTotalOpportunities());
        $this->tester->assertEquals($averageSlotOpportunities, $report->getAverageSlotOpportunities());
        $this->tester->assertEquals($averageImpressions, $report->getAverageImpressions());
        $this->tester->assertEquals($averagePassbacks, $report->getAveragePassbacks());
    }

    /**
     * @test
     */
    public function accountReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getPublisherReport($this->publisher, $params);
        $this->tester->assertNotNull($report);
        $this->tester->assertEquals($report->getReportType()->getReportType(), 'platform.account');

        $adSlots = $this->adSlotManager->getAdSlotsForPublisher($this->publisher);
        //calculate report data
        $totalOpportunities = 0;
        $slotOpportunities = 0;
        $impression = 0;
        $passBack = 0;
        /** @var BaseAdSlotInterface $adSlot */
        foreach($adSlots as $adSlot) {
            $slotOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
            foreach($adSlot->getAdTags() as $adTag) {
                $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
                $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
                $totalOpportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            }
        }
        $fillRate = round($this->getRatio($impression, $slotOpportunities), 4);

        $this->tester->assertEquals($slotOpportunities, $report->getSlotOpportunities());
        $this->tester->assertEquals($totalOpportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($fillRate, $report->getFillRate());

        $reportCount = count($report->getReports());
        $averageTotalOpportunities = $this->getRatio($totalOpportunities, $reportCount);
        $averageImpressions = $this->getRatio($impression, $reportCount);
        $averagePassbacks = $this->getRatio($passBack, $reportCount);
        $averageSlotOpportunities = $this->getRatio($slotOpportunities, $reportCount);

        $this->tester->assertEquals($averageTotalOpportunities, $report->getAverageTotalOpportunities());
        $this->tester->assertEquals($averageSlotOpportunities, $report->getAverageSlotOpportunities());
        $this->tester->assertEquals($averageImpressions, $report->getAverageImpressions());
        $this->tester->assertEquals($averagePassbacks, $report->getAveragePassbacks());
    }

    /**
     * @test
     */
    public function accountByAdNetworksReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getPublisherAdNetworksReport($this->publisher, $params);

        $this->tester->assertNotNull($report);

        foreach($report->getReports() as $adNetworkReport) {
            /** @var AdNetworkInterface $adNetwork */
            $adNetwork = $this->adNetworkManager->find($adNetworkReport->getReportType()->getAdNetwork()->getId());

            $impression = 0;
            $passBack = 0;
            $opportunities = 0;
            $firstOpportunities = 0;
            $verifiedImpression = 0;
            $unverifiedImpression = 0;
            $click = 0;
            $blankImpressions = 0;
            $voidImpressions = 0;
            $adTags = $this->adTagManager->getAdTagIdsForAdNetwork($adNetwork);

            foreach($adTags as $adTag) {
                $opportunities += $this->testEventCounter->getOpportunityCount($adTag);
                $impression += $this->testEventCounter->getImpressionCount($adTag);
                $passBack += $this->testEventCounter->getPassbackCount($adTag);
                $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag);
                $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag);
                $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag);
                $click += $this->testEventCounter->getClickCount($adTag);
                $blankImpressions += $this->testEventCounter->getBlankImpressionCount($adTag);
                $voidImpressions += $this->testEventCounter->getVoidImpressionCount($adTag);
            }

            $fillRate = round($this->getRatio($impression, $opportunities), 4);

            $this->tester->assertEquals($opportunities, $adNetworkReport->getTotalOpportunities());
            $this->tester->assertEquals($impression, $adNetworkReport->getImpressions());
            $this->tester->assertEquals($passBack, $adNetworkReport->getPassbacks());
            $this->tester->assertEquals($firstOpportunities, $adNetworkReport->getFirstOpportunities());
            $this->tester->assertEquals($verifiedImpression, $adNetworkReport->getVerifiedImpressions());
            $this->tester->assertEquals($unverifiedImpression, $adNetworkReport->getUnverifiedImpressions());
            $this->tester->assertEquals($click, $adNetworkReport->getClicks());
            $this->tester->assertEquals($blankImpressions, $adNetworkReport->getBlankImpressions());
            $this->tester->assertEquals($voidImpressions, $adNetworkReport->getVoidImpressions());
            $this->tester->assertEquals($fillRate, $adNetworkReport->getFillRate());

            $reportCount = count($report->getReports());
            $averageTotalOpportunities = $this->getRatio($opportunities, $reportCount);
            $averageImpressions = $this->getRatio($impression, $reportCount);
            $averagePassbacks = $this->getRatio($passBack, $reportCount);
            $averageFirstOpportunities = $this->getRatio($firstOpportunities, $reportCount);
            $averageVoidImpressions = $this->getRatio($voidImpressions, $reportCount);
            $averageBlankImpressions = $this->getRatio($blankImpressions, $reportCount);
            $averageVerifiedImpressions = $this->getRatio($verifiedImpression, $reportCount);
            $averageUnverifiedImpressions = $this->getRatio($unverifiedImpression, $reportCount);
            $averageClicks = $this->getRatio($click, $reportCount);

            $this->tester->assertEquals($averageTotalOpportunities, $report->getAverageTotalOpportunities());
            $this->tester->assertEquals($averageImpressions, $report->getAverageImpressions());
            $this->tester->assertEquals($averagePassbacks, $report->getAveragePassbacks());
            $this->tester->assertEquals($averageFirstOpportunities, $report->getAverageFirstOpportunities());
            $this->tester->assertEquals($averageVoidImpressions, $report->getAverageVoidImpressions());
            $this->tester->assertEquals($averageBlankImpressions, $report->getAverageBlankImpressions());
            $this->tester->assertEquals($averageVerifiedImpressions, $report->getAverageVerifiedImpressions());
            $this->tester->assertEquals($averageUnverifiedImpressions, $report->getAverageUnverifiedImpressions());
            $this->tester->assertEquals($averageClicks, $report->getAverageClicks());
        }
    }

    /**
     * @test
     */
    public function adNetworkReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkReport($this->adNetwork, $params);
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $blankImpression = 0;
        $voidImpressions= 0;
        $click = 0;

        $adTags = $this->adTagManager->getAdTagIdsForAdNetwork($this->adNetwork);
        foreach($adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag);
            $impression += $this->testEventCounter->getImpressionCount($adTag);
            $passBack += $this->testEventCounter->getPassbackCount($adTag);
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag);
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag);
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag);
            $click += $this->testEventCounter->getClickCount($adTag);
            $blankImpression += $this->testEventCounter->getBlankImpressionCount($adTag);
            $voidImpressions += $this->testEventCounter->getVoidImpressionCount($adTag);
        }
        $fillRate = round($this->getRatio($impression, $opportunities), 4);

        $this->tester->assertEquals($opportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $report->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $report->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $report->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $report->getClicks());
        $this->tester->assertEquals($blankImpression, $report->getBlankImpressions());
        $this->tester->assertEquals($voidImpressions, $report->getVoidImpressions());
        $this->tester->assertEquals($fillRate, $report->getFillRate());


        $reportCount = count($report->getReports());
        $averageTotalOpportunities = $this->getRatio($opportunities, $reportCount);
        $averageImpressions = $this->getRatio($impression, $reportCount);
        $averagePassbacks = $this->getRatio($passBack, $reportCount);
        $averageFirstOpportunities = $this->getRatio($firstOpportunities, $reportCount);
        $averageVoidImpressions = $this->getRatio($voidImpressions, $reportCount);
        $averageBlankImpressions = $this->getRatio($blankImpression, $reportCount);
        $averageVerifiedImpressions = $this->getRatio($verifiedImpression, $reportCount);
        $averageUnverifiedImpressions = $this->getRatio($unverifiedImpression, $reportCount);
        $averageClicks = $this->getRatio($click, $reportCount);

        $this->tester->assertEquals($averageTotalOpportunities, $report->getAverageTotalOpportunities());
        $this->tester->assertEquals($averageImpressions, $report->getAverageImpressions());
        $this->tester->assertEquals($averagePassbacks, $report->getAveragePassbacks());
        $this->tester->assertEquals($averageFirstOpportunities, $report->getAverageFirstOpportunities());
        $this->tester->assertEquals($averageVoidImpressions, $report->getAverageVoidImpressions());
        $this->tester->assertEquals($averageVerifiedImpressions, $report->getAverageVerifiedImpressions());
        $this->tester->assertEquals($averageUnverifiedImpressions, $report->getAverageUnverifiedImpressions());
        $this->tester->assertEquals($averageClicks, $report->getAverageClicks());
        $this->tester->assertEquals($averageBlankImpressions, $report->getAverageBlankImpressions());
    }

    /**
     * @test
     */
    public function adNetworkByAdTagsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkAdTagsReport($this->adNetwork, $params);
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $voidImpressions = 0;
        $blankImpressions = 0;
        $click = 0;

        $adTags = $this->adTagManager->getAdTagIdsForAdNetwork($this->adNetwork);
        foreach($adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag);
            $impression += $this->testEventCounter->getImpressionCount($adTag);
            $passBack += $this->testEventCounter->getPassbackCount($adTag);
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag);
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag);
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag);
            $click += $this->testEventCounter->getClickCount($adTag);
            $voidImpressions += $this->testEventCounter->getVoidImpressionCount($adTag);
            $blankImpressions += $this->testEventCounter->getBlankImpressionCount($adTag);
        }
        $fillRate = round($this->getRatio($impression, $opportunities), 4);

        $this->tester->assertEquals($opportunities, $report->getTotalOpportunities(), "opportunities");
        $this->tester->assertEquals($impression, $report->getImpressions(), "impression");
        $this->tester->assertEquals($passBack, $report->getPassbacks(), "passBack");
        $this->tester->assertEquals($firstOpportunities, $report->getFirstOpportunities(), "firstOpportunities");
        $this->tester->assertEquals($verifiedImpression, $report->getVerifiedImpressions(), "verifiedImpression");
        $this->tester->assertEquals($unverifiedImpression, $report->getUnverifiedImpressions(), "unverifiedImpression");
        $this->tester->assertEquals($click, $report->getClicks(), "click");
        $this->tester->assertEquals($voidImpressions, $report->getVoidImpressions(), "voidImpressions");
        $this->tester->assertEquals($blankImpressions, $report->getBlankImpressions(), "blankImpressions");
        $this->tester->assertEquals($fillRate, $report->getFillRate(), "fillRate");
    }

    /**
     * @test
     */
    public function adNetworkBySitesReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkSitesReport($this->adNetwork, $params);
        $this->tester->assertNotNull($report);



        foreach($report->getReports() as $adNetworkReport) {
            $impression = 0;
            $passBack = 0;
            $opportunities = 0;
            $firstOpportunities = 0;
            $verifiedImpression = 0;
            $unverifiedImpression = 0;
            $blankImpressions = 0;
            $voidImpressions = 0;
            $click = 0;

            $site = $this->siteManager->find($adNetworkReport->getReportType()->getSite()->getId());
            $adTags = $this->adTagManager->getAdTagIdsForSite($site);
            foreach($adTags as $adTag) {
                $opportunities += $this->testEventCounter->getOpportunityCount($adTag);
                $impression += $this->testEventCounter->getImpressionCount($adTag);
                $blankImpressions += $this->testEventCounter->getBlankImpressionCount($adTag);
                $voidImpressions += $this->testEventCounter->getVoidImpressionCount($adTag);
                $passBack += $this->testEventCounter->getPassbackCount($adTag);
                $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag);
                $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag);
                $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag);
                $click += $this->testEventCounter->getClickCount($adTag);
            }
            $fillRate = round($this->getRatio($impression, $opportunities), 4);

            $this->tester->assertEquals($opportunities, $adNetworkReport->getTotalOpportunities());
            $this->tester->assertEquals($impression, $adNetworkReport->getImpressions());
            $this->tester->assertEquals($blankImpressions, $adNetworkReport->getBlankImpressions());
            $this->tester->assertEquals($voidImpressions, $adNetworkReport->getVoidImpressions());
            $this->tester->assertEquals($passBack, $adNetworkReport->getPassbacks());
            $this->tester->assertEquals($firstOpportunities, $adNetworkReport->getFirstOpportunities());
            $this->tester->assertEquals($verifiedImpression, $adNetworkReport->getVerifiedImpressions());
            $this->tester->assertEquals($unverifiedImpression, $adNetworkReport->getUnverifiedImpressions());
            $this->tester->assertEquals($click, $adNetworkReport->getClicks());
            $this->tester->assertEquals($fillRate, $adNetworkReport->getFillRate());
        }
    }

    /**
     * @test
     */
    public function adNetworkWithSingleSiteByDaysReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkSiteReport($this->adNetwork, $this->site, $params);
        $this->tester->assertNotNull($report);

        $impression = 0;
        $blankImpressions = 0;
        $voidImpressions = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;
        $adTags = $this->adTagManager->getAdTagIdsForSite($this->site);
        foreach($adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag);
            $impression += $this->testEventCounter->getImpressionCount($adTag);
            $blankImpressions += $this->testEventCounter->getBlankImpressionCount($adTag);
            $voidImpressions += $this->testEventCounter->getVoidImpressionCount($adTag);
            $passBack += $this->testEventCounter->getPassbackCount($adTag);
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag);
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag);
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag);
            $click += $this->testEventCounter->getClickCount($adTag);
        }
        $fillRate = round($this->getRatio($impression, $opportunities), 4);

        $this->tester->assertEquals($opportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($blankImpressions, $report->getBlankImpressions());
        $this->tester->assertEquals($voidImpressions, $report->getVoidImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $report->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $report->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $report->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $report->getClicks());
        $this->tester->assertEquals($fillRate, $report->getFillRate());
    }

    /**
     * @test
     */
    public function adNetworkWithSingleSiteByAdTagsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkSiteAdTagsReport($this->adNetwork, $this->site, $params);
        $this->tester->assertNotNull($report);

        $impression = 0;
        $blankImpressions = 0;
        $voidImpressions = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;
        $adTags = $this->adTagManager->getAdTagIdsForSite($this->site);
        foreach($adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag);
            $impression += $this->testEventCounter->getImpressionCount($adTag);
            $blankImpressions += $this->testEventCounter->getBlankImpressionCount($adTag);
            $voidImpressions += $this->testEventCounter->getVoidImpressionCount($adTag);
            $passBack += $this->testEventCounter->getPassbackCount($adTag);
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag);
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag);
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag);
            $click += $this->testEventCounter->getClickCount($adTag);
        }
        $fillRate = round($this->getRatio($impression, $opportunities), 4);

        $this->tester->assertEquals($opportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($blankImpressions, $report->getBlankImpressions());
        $this->tester->assertEquals($voidImpressions, $report->getVoidImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $report->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $report->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $report->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $report->getClicks());
        $this->tester->assertEquals($fillRate, $report->getFillRate());
    }

    /**
     * @test
     */
    public function sitesReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getPublisherSitesReport($this->publisher, $params);
        $this->tester->assertNotNull($report);

        foreach($report->getReports() as $siteReport) {
            /** @var BaseAdSlotInterface $adSlot */
            $site = $this->siteManager->find($siteReport->getReportType()->getSite()->getId());
            $adSlots = $this->adSlotManager->getAdSlotsForSite($site);
            $slotOpportunities = 0;
            $totalOpportunities= 0;
            $impression = 0;
            $passBack = 0;
            foreach($adSlots as $adSlot) {
                $slotOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
                foreach($adSlot->getAdTags() as $adTag) {
                    $totalOpportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
                    $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
                    $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
                }
            }
            $fillRate = round($this->getRatio($impression, $slotOpportunities), 4);

            $this->tester->assertEquals($slotOpportunities, $siteReport->getSlotOpportunities());
            $this->tester->assertEquals($totalOpportunities, $siteReport->getTotalOpportunities());
            $this->tester->assertEquals($impression, $siteReport->getImpressions());
            $this->tester->assertEquals($passBack, $siteReport->getPassbacks());
            $this->tester->assertEquals($fillRate, $siteReport->getFillRate());

        }
    }

    /**
     * @test
     */
    public function siteByDayReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getSiteReport($this->site, $params);
        $this->tester->assertNotNull($report);

        $adSlots = $this->adSlotManager->getAdSlotsForSite($this->site);
        $slotOpportunities = 0;
        $totalOpportunities= 0;
        $impression = 0;
        $passBack = 0;
        foreach($adSlots as $adSlot) {
            $slotOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
            foreach($adSlot->getAdTags() as $adTag) {
                $totalOpportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
                $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
                $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            }
        }
        $fillRate = round($this->getRatio($impression, $slotOpportunities), 4);

        $this->tester->assertEquals($slotOpportunities, $report->getSlotOpportunities());
        $this->tester->assertEquals($totalOpportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($fillRate, $report->getFillRate());
    }

    /**
     * @test
     */
    public function siteByAdSlotsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getSiteAdSlotsReport($this->site, $params);
        $this->tester->assertNotNull($report);

        foreach($report->getReports() as $siteReport) {
            $adSlot = $siteReport->getReportType()->getAdSlot();
            $this->tester->assertEquals($this->testEventCounter->getSlotOpportunityCount($adSlot->getId()), $siteReport->getSlotOpportunities());
            $totalOpportunities = 0;
            $impression = 0;
            $passBack = 0;
            foreach($adSlot->getAdTags() as $adTag) {
                $totalOpportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
                $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
                $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            }
            $fillRate = round($this->getRatio($impression, $siteReport->getSlotOpportunities()), 4);

            $this->assertEquals($totalOpportunities, $siteReport->getTotalOpportunities());
            $this->assertEquals($impression, $siteReport->getImpressions());
            $this->assertEquals($passBack, $siteReport->getPassbacks());
            $this->assertEquals($fillRate, $siteReport->getFillRate());
        }
    }

    /**
     * @test
     */
    public function siteByAdTagsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, true, true);
        $report = $this->reportBuilder->getSiteAdTagsReport($this->site, $params);
        $siteReport = $report->getOriginalResult();

        $this->tester->assertNotNull($report);
        $adTags = $this->adTagManager->getAdTagIdsForSite($siteReport->getReportType()->getSite());

        $totalOpportunities = 0;
        $impression = 0;
        $passBack = 0;
        foreach($adTags as $adTag) {
            $totalOpportunities += $this->testEventCounter->getOpportunityCount($adTag);
            $impression += $this->testEventCounter->getImpressionCount($adTag);
            $passBack += $this->testEventCounter->getPassbackCount($adTag);
        }
        $fillRate = round($this->getRatio($impression, $siteReport->getSlotOpportunities()), 4);

        $this->tester->assertEquals($totalOpportunities, $siteReport->getTotalOpportunities());
        $this->tester->assertEquals($impression, $siteReport->getImpressions());
        $this->tester->assertEquals($passBack, $siteReport->getPassbacks());
        $this->tester->assertEquals($fillRate, $siteReport->getFillRate());
    }

    /**
     * @test
     */
    public function siteByAdNetworksReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, true, true);
        $report = $this->reportBuilder->getSiteAdNetworksReport($this->site, $params);
        $this->tester->assertNotNull($report);

        $impression = 0;
        $voidImpression = 0;
        $blankImpression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;

        $reportType = current($report->getReportType());
        $adTags = $this->adTagManager->getAdTagIdsForSite($reportType->getSite());

        foreach($adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag);
            $impression += $this->testEventCounter->getImpressionCount($adTag);
            $voidImpression += $this->testEventCounter->getVoidImpressionCount($adTag);
            $blankImpression += $this->testEventCounter->getBlankImpressionCount($adTag);
            $passBack += $this->testEventCounter->getPassbackCount($adTag);
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag);
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag);
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag);
            $click += $this->testEventCounter->getClickCount($adTag);
        }
        $fillRate = round($this->getRatio($impression, $opportunities), 4);

        $this->tester->assertEquals($opportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($voidImpression, $report->getVoidImpressions());
        $this->tester->assertEquals($blankImpression, $report->getBlankImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $report->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $report->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $report->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $report->getClicks());
        $this->tester->assertEquals($fillRate, $report->getFillRate());
    }

    /**
     * @test
     */
    public function adSlotByDayReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, true, true);
        $report = $this->reportBuilder->getAdSlotReport($this->adSlots[0], $params);
        $this->tester->assertNotNull($report);

        $adSlot = $report->getReportType()->getAdSlot();
        //calculate report data
        $slotOpportunities = $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
        $totalOpportunities = 0;

        $impression = 0;
        $passBack = 0;
        foreach($adSlot->getAdTags() as $adTag) {
            $totalOpportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }
        $fillRate = round($this->getRatio($impression, $slotOpportunities), 4);

        $this->tester->assertEquals($slotOpportunities, $report->getSlotOpportunities());
        $this->tester->assertEquals($totalOpportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($fillRate, $report->getFillRate());
    }

    /**
     * @test
     */
    public function adSlotByAdTagsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, true, true);
        $report = $this->reportBuilder->getAdSlotReport($this->adSlots[0], $params);
        $this->tester->assertNotNull($report);

        $adSlot = $report->getReportType()->getAdSlot();
        //calculate report data
        $slotOpportunities = $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
        $totalOpportunities = 0;

        $impression = 0;
        $passBack = 0;
        foreach($adSlot->getAdTags() as $adTag) {
            $totalOpportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }
        $fillRate = round($this->getRatio($impression, $slotOpportunities), 4);

        $this->tester->assertEquals($slotOpportunities, $report->getSlotOpportunities());
        $this->tester->assertEquals($totalOpportunities, $report->getTotalOpportunities());
        $this->tester->assertEquals($impression, $report->getImpressions());
        $this->tester->assertEquals($passBack, $report->getPassbacks());
        $this->tester->assertEquals($fillRate, $report->getFillRate());
    }

    /**
     * @test
     */
    public function adTagReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdTagReport($this->adTags[0], $params);

        $this->tester->assertNotNull($report);
        $this->tester->assertEquals($this->testEventCounter->getOpportunityCount($this->adTags[0]->getId()), $report->getTotalOpportunities());
        $this->tester->assertEquals($this->testEventCounter->getImpressionCount($this->adTags[0]->getId()), $report->getImpressions());
        $this->tester->assertEquals($this->testEventCounter->getVoidImpressionCount($this->adTags[0]->getId()), $report->getVoidImpressions());
        $this->tester->assertEquals($this->testEventCounter->getBlankImpressionCount($this->adTags[0]->getId()), $report->getBlankImpressions());
        $this->tester->assertEquals($this->testEventCounter->getPassbackCount($this->adTags[0]->getId()), $report->getPassbacks());
        $this->tester->assertEquals($this->testEventCounter->getFirstOpportunityCount($this->adTags[0]->getId()), $report->getFirstOpportunities());
        $this->tester->assertEquals($this->testEventCounter->getVerifiedImpressionCount($this->adTags[0]->getId()), $report->getVerifiedImpressions());
        $this->tester->assertEquals($this->testEventCounter->getUnverifiedImpressionCount($this->adTags[0]->getId()), $report->getUnverifiedImpressions());
        $this->tester->assertEquals($this->testEventCounter->getClickCount($this->adTags[0]->getId()), $report->getClicks());
    }
}