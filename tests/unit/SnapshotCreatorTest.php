<?php


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;

class SnapshotCreatorTest extends \Codeception\TestCase\Test
{
    /** @var \UnitTester */
    protected $tester;
    /** @var EntityManagerInterface $em */
    protected $em;
    /** @var PublisherManagerInterface $publisherManager */
    protected $publisherManager;
    /** @var AdSlotManagerInterface */
    protected $adSlotManager;
    /** @var ReportBuilderInterface */
    protected $reportBuilder;
    /** @var EventCounterInterface */
    protected $testEventCounter;

    protected $publisher;
    protected $adNetwork;
    protected $site;
    /** @var array */
    protected $adSlots;

    /** @var array */
    protected $adTags;


    protected function _before()
    {
        $this->em = $this->tester->grabServiceFromContainer('doctrine.orm.entity_manager');
        $this->publisherManager = $this->tester->grabServiceFromContainer('tagcade_user.domain_manager.publisher');
        $this->adSlotManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.ad_slot');
        $this->reportBuilder = $this->tester->grabServiceFromContainer('tagcade.server.report.performance_report.display.selector.report_builder');

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
        $this->tester->assertEquals($report->getReportType()->getReportType(), 'platform.platform');
    }

    /**
     * @test
     */
    public function accountReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getPublisherReport($this->publisher, $params);
        $accountReport = current($report->getReports());
        $this->tester->assertNotNull($report);
        $this->tester->assertEquals($report->getReportType()->getReportType(), 'platform.account');

        //calculate report data
        $totalOpportunities = 0;
        /** @var BaseAdSlotInterface $adSlot */
        foreach($this->adSlots as $adSlot) {
            $totalOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
        }

        $impression = 0;
        $passBack = 0;
        foreach($this->adTags as $adTag) {
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }

        $this->tester->assertEquals($totalOpportunities, $accountReport->getSlotOpportunities());
        $this->tester->assertEquals($impression, $accountReport->getImpressions());
        $this->tester->assertEquals($passBack, $accountReport->getPassbacks());
    }

    /**
     * @test
     */
    public function accountByAdNetworksReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getPublisherAdNetworksReport($this->publisher, $params);
        $adNetworkReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;

        /** @var AdTagInterface $adTag */
        foreach($this->adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag->getId());
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag->getId());
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag->getId());
            $click += $this->testEventCounter->getClickCount($adTag->getId());
        }

        $this->tester->assertEquals($opportunities, $adNetworkReport->getTotalOpportunities());
        $this->tester->assertEquals($impression, $adNetworkReport->getImpressions());
        $this->tester->assertEquals($passBack, $adNetworkReport->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $adNetworkReport->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $adNetworkReport->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $adNetworkReport->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $adNetworkReport->getClicks());
    }

    /**
     * @test
     */
    public function adNetworkReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkReport($this->adNetwork, $params);
        $adNetworkReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;

        /** @var AdTagInterface $adTag */
        foreach($this->adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag->getId());
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag->getId());
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag->getId());
            $click += $this->testEventCounter->getClickCount($adTag->getId());
        }

        $this->tester->assertEquals($opportunities, $adNetworkReport->getTotalOpportunities());
        $this->tester->assertEquals($impression, $adNetworkReport->getImpressions());
        $this->tester->assertEquals($passBack, $adNetworkReport->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $adNetworkReport->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $adNetworkReport->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $adNetworkReport->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $adNetworkReport->getClicks());
    }

    /**
     * @test
     */
    public function adNetworkByAdTagsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkAdTagsReport($this->adNetwork, $params);
        $adNetworkReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;

        /** @var AdTagInterface $adTag */
        foreach($this->adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag->getId());
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag->getId());
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag->getId());
            $click += $this->testEventCounter->getClickCount($adTag->getId());
        }

        $this->tester->assertEquals($opportunities, $adNetworkReport->getTotalOpportunities());
        $this->tester->assertEquals($impression, $adNetworkReport->getImpressions());
        $this->tester->assertEquals($passBack, $adNetworkReport->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $adNetworkReport->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $adNetworkReport->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $adNetworkReport->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $adNetworkReport->getClicks());
    }

    /**
     * @test
     */
    public function adNetworkBySitesReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkSitesReport($this->adNetwork, $params);
        $adNetworkReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;

        /** @var AdTagInterface $adTag */
        foreach($this->adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag->getId());
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag->getId());
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag->getId());
            $click += $this->testEventCounter->getClickCount($adTag->getId());
        }

        $this->tester->assertEquals($opportunities, $adNetworkReport->getTotalOpportunities());
        $this->tester->assertEquals($impression, $adNetworkReport->getImpressions());
        $this->tester->assertEquals($passBack, $adNetworkReport->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $adNetworkReport->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $adNetworkReport->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $adNetworkReport->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $adNetworkReport->getClicks());
    }

    /**
     * @test
     */
    public function adNetworkWithSingleSiteByDaysReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkSiteReport($this->adNetwork, $this->site, $params);
        $adNetworkReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;

        /** @var AdTagInterface $adTag */
        foreach($this->adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag->getId());
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag->getId());
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag->getId());
            $click += $this->testEventCounter->getClickCount($adTag->getId());
        }

        $this->tester->assertEquals($opportunities, $adNetworkReport->getTotalOpportunities());
        $this->tester->assertEquals($impression, $adNetworkReport->getImpressions());
        $this->tester->assertEquals($passBack, $adNetworkReport->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $adNetworkReport->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $adNetworkReport->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $adNetworkReport->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $adNetworkReport->getClicks());
    }

    /**
     * @test
     */
    public function adNetworkWithSingleSiteByAdTagsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getAdNetworkSiteAdTagsReport($this->adNetwork, $this->site, $params);
        $adNetworkReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;

        /** @var AdTagInterface $adTag */
        foreach($this->adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag->getId());
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag->getId());
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag->getId());
            $click += $this->testEventCounter->getClickCount($adTag->getId());
        }

        $this->tester->assertEquals($opportunities, $adNetworkReport->getTotalOpportunities());
        $this->tester->assertEquals($impression, $adNetworkReport->getImpressions());
        $this->tester->assertEquals($passBack, $adNetworkReport->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $adNetworkReport->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $adNetworkReport->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $adNetworkReport->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $adNetworkReport->getClicks());
    }

    /**
     * @test
     */
    public function sitesReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getPublisherSitesReport($this->publisher, $params);
        $siteReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        //calculate report data
        $totalOpportunities = 0;
        /** @var BaseAdSlotInterface $adSlot */
        foreach($this->adSlots as $adSlot) {
            $totalOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
        }

        $impression = 0;
        $passBack = 0;
        foreach($this->adTags as $adTag) {
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }

        $this->tester->assertEquals($totalOpportunities, $siteReport->getSlotOpportunities());
        $this->tester->assertEquals($impression, $siteReport->getImpressions());
        $this->tester->assertEquals($passBack, $siteReport->getPassbacks());
    }

    /**
     * @test
     */
    public function siteByDayReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getSiteReport($this->site, $params);
        $siteReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        //calculate report data
        $totalOpportunities = 0;
        /** @var BaseAdSlotInterface $adSlot */
        foreach($this->adSlots as $adSlot) {
            $totalOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
        }

        $impression = 0;
        $passBack = 0;
        foreach($this->adTags as $adTag) {
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }

        $this->tester->assertEquals($totalOpportunities, $siteReport->getSlotOpportunities());
        $this->tester->assertEquals($impression, $siteReport->getImpressions());
        $this->tester->assertEquals($passBack, $siteReport->getPassbacks());
    }

    /**
     * @test
     */
    public function siteByAdSlotsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, false, true);
        $report = $this->reportBuilder->getSiteAdSlotsReport($this->site, $params);
        $siteReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        //calculate report data
        $totalOpportunities = 0;
        /** @var BaseAdSlotInterface $adSlot */
        foreach($this->adSlots as $adSlot) {
            $totalOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
        }

        $impression = 0;
        $passBack = 0;
        foreach($this->adTags as $adTag) {
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }

        $this->tester->assertEquals($totalOpportunities, $siteReport->getSlotOpportunities());
        $this->tester->assertEquals($impression, $siteReport->getImpressions());
        $this->tester->assertEquals($passBack, $siteReport->getPassbacks());
    }

    /**
     * @test
     */
    public function siteByAdTagsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, true, true);
        $report = $this->reportBuilder->getSiteAdTagsReport($this->site, $params);
        $siteReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        //calculate report data
        $totalOpportunities = 0;
        /** @var BaseAdSlotInterface $adSlot */
        foreach($this->adSlots as $adSlot) {
            $totalOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());
        }

        $impression = 0;
        $passBack = 0;
        foreach($this->adTags as $adTag) {
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }

        $this->tester->assertEquals($totalOpportunities, $siteReport->getSlotOpportunities());
        $this->tester->assertEquals($impression, $siteReport->getImpressions());
        $this->tester->assertEquals($passBack, $siteReport->getPassbacks());
    }

    /**
     * @test
     */
    public function siteByAdNetworksReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, true, true);
        $report = $this->reportBuilder->getSiteAdNetworksReport($this->site, $params);
        $siteReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        $impression = 0;
        $passBack = 0;
        $opportunities = 0;
        $firstOpportunities = 0;
        $verifiedImpression = 0;
        $unverifiedImpression = 0;
        $click = 0;

        /** @var AdTagInterface $adTag */
        foreach($this->adTags as $adTag) {
            $opportunities += $this->testEventCounter->getOpportunityCount($adTag->getId());
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
            $firstOpportunities += $this->testEventCounter->getFirstOpportunityCount($adTag->getId());
            $verifiedImpression += $this->testEventCounter->getVerifiedImpressionCount($adTag->getId());
            $unverifiedImpression += $this->testEventCounter->getUnverifiedImpressionCount($adTag->getId());
            $click += $this->testEventCounter->getClickCount($adTag->getId());
        }

        $this->tester->assertEquals($opportunities, $siteReport->getTotalOpportunities());
        $this->tester->assertEquals($impression, $siteReport->getImpressions());
        $this->tester->assertEquals($passBack, $siteReport->getPassbacks());
        $this->tester->assertEquals($firstOpportunities, $siteReport->getFirstOpportunities());
        $this->tester->assertEquals($verifiedImpression, $siteReport->getVerifiedImpressions());
        $this->tester->assertEquals($unverifiedImpression, $siteReport->getUnverifiedImpressions());
        $this->tester->assertEquals($click, $siteReport->getClicks());
    }

    /**
     * @test
     */
    public function adSlotByDayReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, true, true);
        $report = $this->reportBuilder->getAdSlotReport($this->adSlots[0], $params);
        $adSlotReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        //calculate report data
        $totalOpportunities = 0;
        /** @var BaseAdSlotInterface $adSlot */
        $adSlot = $this->adSlots[0];
        $totalOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());

        $impression = 0;
        $passBack = 0;
        foreach($adSlot->getAdTags() as $adTag) {
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }

        $this->tester->assertEquals($totalOpportunities, $adSlotReport->getSlotOpportunities());
        $this->tester->assertEquals($impression, $adSlotReport->getImpressions());
        $this->tester->assertEquals($passBack, $adSlotReport->getPassbacks());
    }

    /**
     * @test
     */
    public function adSlotByAdTagsReport()
    {
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('today'), null, true, true);
        $report = $this->reportBuilder->getAdSlotReport($this->adSlots[0], $params);
        $adSlotReport = current($report->getReports());
        $this->tester->assertNotNull($report);

        //calculate report data
        $totalOpportunities = 0;
        /** @var BaseAdSlotInterface $adSlot */
        $adSlot = $this->adSlots[0];
        $totalOpportunities += $this->testEventCounter->getSlotOpportunityCount($adSlot->getId());

        $impression = 0;
        $passBack = 0;
        foreach($adSlot->getAdTags() as $adTag) {
            $impression += $this->testEventCounter->getImpressionCount($adTag->getId());
            $passBack += $this->testEventCounter->getPassbackCount($adTag->getId());
        }

        $this->tester->assertEquals($totalOpportunities, $adSlotReport->getSlotOpportunities());
        $this->tester->assertEquals($impression, $adSlotReport->getImpressions());
        $this->tester->assertEquals($passBack, $adSlotReport->getPassbacks());
    }
}