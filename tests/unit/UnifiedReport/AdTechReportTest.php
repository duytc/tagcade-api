<?php


use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface;
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
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilderInterface as UnifiedReportBuilderInterface;

class AdTechReportTest extends \Codeception\TestCase\Test
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

    /** @var SubPublisherManagerInterface */
    protected $subPublisherManager;

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

    /**
     * @var AdNetworkRepositoryInterface
     */
    protected $adNetworkRepository;

    /** @var RonAdSlotManagerInterface */
    protected $ronAdSlotManager;

    /** @var LibrarySlotTagManagerInterface */
    protected $librarySlotTagManager;

    /** @var ReportBuilderInterface */
    protected $performanceReportBuilder;

    /** @var UnifiedReportBuilderInterface */
    protected $unifiedReportBuilder;

    /** @var EventCounterInterface */
    protected $testEventCounter;

    /** @var LoggerInterface */
    protected $logger;

    protected $publisher;
    protected $subPublisher;
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
        $this->subPublisherManager = $this->tester->grabServiceFromContainer('tagcade_user.domain_manager.sub_publisher');
        $this->adSlotManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.ad_slot');
        $this->siteManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.site');
        $this->adNetworkManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.ad_network');
        $this->adNetworkRepository = $this->tester->grabServiceFromContainer('tagcade.repository.ad_network');
        $this->adTagManager = $this->tester->grabServiceFromContainer('tagcade.domain_manager.ad_tag');
        $this->performanceReportBuilder = $this->tester->grabServiceFromContainer('tagcade.service.report.performance_report.display.selector.report_builder');
        $this->unifiedReportBuilder = $this->tester->grabServiceFromContainer('tagcade.service.report.unified_report.selector.report_builder');
        $this->logger = $this->tester->grabServiceFromContainer('logger');

        // import sample report files
        $cmd = sprintf('%s tc:unified-report:import %s --publisher=%d --partnerCName=%s --start-date=%s --end-date=%s --override --keep-files',
            $this->getImporterAppConsoleCommand(),
            '/var/www/api.tagcade.dev/tests/_data/unified_report/ad-tech/ad_tech_report.xls',
            2,
            'ad-tech',
            '2016-05-01',
            '2016-06-01'
        );
        $this->executeProcess($process = new Process($cmd), ['timeout' => 200], $this->logger);
    }

    protected function _after()
    {
    }

    /**
     * @test
     */
    public function platformReport()
    {
        $publisher = $this->publisherManager->find(2);
//        $subPublisher = $this->subPublisherManager->find(3);
        $adNetwork = $this->adNetworkRepository->getAdNetworkByPublisherAndPartnerCName(2, 'ad-tech');
        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('2016-05-15'), new \DateTime('2016-05-15'), false, true);

        $allPartnerBySiteReport = $this->unifiedReportBuilder->getAllDemandPartnersBySiteReport($publisher, $params);
        $this->tester->assertNotNull($allPartnerBySiteReport);

        $allPartnerByDayReport = $this->unifiedReportBuilder->getAllDemandPartnersByDayReport($publisher, $params);
        $this->tester->assertNotNull($allPartnerByDayReport);

        $allPartnerByAdTagReport = $this->unifiedReportBuilder->getAllDemandPartnersByAdTagReport($publisher, $params);
        $this->tester->assertNotNull($allPartnerByAdTagReport);

        $allPartnerByPartnerReport = $this->unifiedReportBuilder->getAllDemandPartnersByPartnerReport($publisher, $params);
        $this->tester->assertNotNull($allPartnerByAdTagReport);

        $partnerByDayReport = $this->unifiedReportBuilder->getPartnerAllSitesByDayReport($adNetwork, $params);
        $this->tester->assertNotNull($partnerByDayReport);

        $this->tester->assertEquals($allPartnerByDayReport->getImpressions(), $allPartnerByAdTagReport->getImpressions());
        $this->tester->assertEquals($allPartnerByDayReport->getTotalOpportunities(), $allPartnerByAdTagReport->getTotalOpportunities());
        $this->tester->assertEquals($allPartnerByDayReport->getPassbacks(), $allPartnerByAdTagReport->getPassbacks());
        $this->tester->assertEquals($allPartnerByDayReport->getEstRevenue(), $allPartnerByAdTagReport->getEstRevenue());
        $this->tester->assertEquals($allPartnerByDayReport->getEstCpm(), $allPartnerByAdTagReport->getEstCpm());

        $this->tester->assertEquals($allPartnerByDayReport->getImpressions(), $allPartnerByPartnerReport->getImpressions());
        $this->tester->assertEquals($allPartnerByDayReport->getTotalOpportunities(), $allPartnerByPartnerReport->getTotalOpportunities());
        $this->tester->assertEquals($allPartnerByDayReport->getPassbacks(), $allPartnerByPartnerReport->getPassbacks());
        $this->tester->assertEquals($allPartnerByDayReport->getEstRevenue(), $allPartnerByPartnerReport->getEstRevenue());
        $this->tester->assertEquals($allPartnerByDayReport->getEstCpm(), $allPartnerByPartnerReport->getEstCpm());

        $this->tester->assertEquals($allPartnerByDayReport->getImpressions(), $partnerByDayReport->getImpressions());
        $this->tester->assertEquals($allPartnerByDayReport->getTotalOpportunities(), $partnerByDayReport->getTotalOpportunities());
        $this->tester->assertEquals($allPartnerByDayReport->getPassbacks(), $partnerByDayReport->getPassbacks());
        $this->tester->assertEquals($allPartnerByDayReport->getEstRevenue(), $partnerByDayReport->getEstRevenue());
        $this->tester->assertEquals($allPartnerByDayReport->getEstCpm(), $partnerByDayReport->getEstCpm());

        $this->tester->assertEquals($allPartnerByDayReport->getImpressions(), $allPartnerBySiteReport->getImpressions());
        $this->tester->assertEquals($allPartnerByDayReport->getTotalOpportunities(), $allPartnerBySiteReport->getTotalOpportunities());
        $this->tester->assertEquals($allPartnerByDayReport->getPassbacks(), $allPartnerBySiteReport->getPassbacks());
        $this->tester->assertEquals($allPartnerByDayReport->getEstRevenue(), $allPartnerBySiteReport->getEstRevenue());
        $this->tester->assertEquals($allPartnerByDayReport->getEstCpm(), $allPartnerBySiteReport->getEstCpm());


        $params = new Tagcade\Service\Report\PerformanceReport\Display\Selector\Params(new \DateTime('2016-05-01'), new \DateTime('2016-06-01'), false, true);
        $allPartnerByDayReport = $this->unifiedReportBuilder->getAllDemandPartnersByDayReport($publisher, $params);
        $this->tester->assertNotNull($allPartnerByDayReport);

        $allPartnerBySiteReport = $this->unifiedReportBuilder->getAllDemandPartnersBySiteReport($publisher, $params);
        $this->tester->assertNotNull($allPartnerBySiteReport);

        $allPartnerByAdTagReport = $this->unifiedReportBuilder->getAllDemandPartnersByAdTagReport($publisher, $params);
        $this->tester->assertNotNull($allPartnerByAdTagReport);

        $allPartnerByPartnerReport = $this->unifiedReportBuilder->getAllDemandPartnersByPartnerReport($publisher, $params);
        $this->tester->assertNotNull($allPartnerByAdTagReport);

        $partnerByDayReport = $this->unifiedReportBuilder->getPartnerAllSitesByDayReport($adNetwork, $params);
        $this->tester->assertNotNull($partnerByDayReport);

        $this->tester->assertEquals($allPartnerByDayReport->getImpressions(), $allPartnerByAdTagReport->getImpressions());
        $this->tester->assertEquals($allPartnerByDayReport->getTotalOpportunities(), $allPartnerByAdTagReport->getTotalOpportunities());
        $this->tester->assertEquals($allPartnerByDayReport->getPassbacks(), $allPartnerByAdTagReport->getPassbacks());
        $this->tester->assertEquals($allPartnerByDayReport->getEstRevenue(), $allPartnerByAdTagReport->getEstRevenue());
        $this->tester->assertEquals($allPartnerByDayReport->getEstCpm(), $allPartnerByAdTagReport->getEstCpm());

        $this->tester->assertEquals($allPartnerByDayReport->getImpressions(), $allPartnerByPartnerReport->getImpressions());
        $this->tester->assertEquals($allPartnerByDayReport->getTotalOpportunities(), $allPartnerByPartnerReport->getTotalOpportunities());
        $this->tester->assertEquals($allPartnerByDayReport->getPassbacks(), $allPartnerByPartnerReport->getPassbacks());
        $this->tester->assertEquals($allPartnerByDayReport->getEstRevenue(), $allPartnerByPartnerReport->getEstRevenue());
        $this->tester->assertEquals($allPartnerByDayReport->getEstCpm(), $allPartnerByPartnerReport->getEstCpm());

        $this->tester->assertEquals($allPartnerByDayReport->getImpressions(), $partnerByDayReport->getImpressions());
        $this->tester->assertEquals($allPartnerByDayReport->getTotalOpportunities(), $partnerByDayReport->getTotalOpportunities());
        $this->tester->assertEquals($allPartnerByDayReport->getPassbacks(), $partnerByDayReport->getPassbacks());
        $this->tester->assertEquals($allPartnerByDayReport->getEstRevenue(), $partnerByDayReport->getEstRevenue());
        $this->tester->assertEquals($allPartnerByDayReport->getEstCpm(), $partnerByDayReport->getEstCpm());

        $this->tester->assertEquals($allPartnerByDayReport->getImpressions(), $allPartnerBySiteReport->getImpressions());
        $this->tester->assertEquals($allPartnerByDayReport->getTotalOpportunities(), $allPartnerBySiteReport->getTotalOpportunities());
        $this->tester->assertEquals($allPartnerByDayReport->getPassbacks(), $allPartnerBySiteReport->getPassbacks());
        $this->tester->assertEquals($allPartnerByDayReport->getEstRevenue(), $allPartnerBySiteReport->getEstRevenue());
        $this->tester->assertEquals($allPartnerByDayReport->getEstCpm(), $allPartnerBySiteReport->getEstCpm());
    }

    protected function getImporterAppConsoleCommand()
    {
        $pathToSymfonyConsole = '/home/vagrant/tagcade/report-importer/app';
        $command = sprintf('php %s/console', $pathToSymfonyConsole);

        return $command;
    }

    protected function executeProcess(Process $process, array $options, LoggerInterface $logger)
    {
        if (array_key_exists('timeout', $options)) {
            $process->setTimeout($options['timeout']);
        }

        $process->mustRun(function($type, $buffer) use($logger) {
            if (Process::ERR === $type) {
                $logger->error($buffer);
            } else {
                $logger->info($buffer);
            }
        }
        );
    }
}