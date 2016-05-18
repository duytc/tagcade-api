<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\LibraryAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryAdTagRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class GenerateDummyDataCommand extends ContainerAwareCommand
{
    const AD_SLOT_TYPE_DISPLAY = 'display';
    const AD_SLOT_TYPE_NATIVE = 'native';

    const GENERATE_PERFORMANCE_REPORT_COMMAND = 'tc:dummy-performance-report:create';
    const GENERATE_SOURCE_REPORT_COMMAND = 'tc:dummy-source-report:create';

    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:dummy-data:create')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'configuration file to generate dummy data', 'dev/dummy-data/tagcade.yml')
            ->setDescription('Generate dummy data');;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ymlConfig = $input->getOption('config');
        if (!file_exists($ymlConfig) || !is_file($ymlConfig)) {
            throw new RuntimeException('Expect a valid configuration file.');
        }

        $ymlParser = new Parser();
        $config = $ymlParser->parse(file_get_contents($ymlConfig));
        /**
         * @var EntityManagerInterface $em
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /**
         * @var PublisherManagerInterface $publisherManager
         */
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        foreach ($config as $name => $publisherData) {
            if (!is_array($publisherData)) {
                throw new \Exception(sprintf('Invalid configuration for publisher %s.', $name));
            }

            if (!array_key_exists('id', $publisherData)) {
                throw new \Exception(sprintf('Expect id for publisher %s. None is given', $name));
            }

            $publisherId = $publisherData['id'];
            $pub = $publisherManager->findPublisher($publisherId);

            if (!$pub instanceof PublisherInterface) {
                throw new \Exception(sprintf('Publisher with id %d does not exist', $publisherId));
            }

            // TODO validate yml config to have proper referencing: etc: ad tag => ad network and available ad networks

            if (array_key_exists('adNetworks', $publisherData)) {
                $this->createAdNetworksForPublisher($pub, $publisherData['adNetworks']);
            }

            if (array_key_exists('sites', $publisherData)) {
                $this->createSitesForPublisher($pub, $publisherData['sites']);
            }

            $em->flush();

            // Now we create report data
            if (array_key_exists('reports', $publisherData)) {
                $this->createReportsForPublisher($pub, $publisherData['reports']);
            }
        }

    }

    protected function createAdNetworksForPublisher(PublisherInterface $publisher, array $adNetworkData)
    {
        /**
         * @var AdNetworkRepositoryInterface $adNetworkRepository
         */
        $adNetworkRepository = $this->getContainer()->get('tagcade.repository.ad_network');
        /**
         * @var EntityManagerInterface $em
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        foreach ($adNetworkData as $name => $data) {
            $nw = $adNetworkRepository->findBy(array('name' => $name));
            if (!empty($nw)) {
                continue;
            }

            $nw = new AdNetwork();
            $nw->setName($name);
            $nw->setPublisher($publisher);

            if (null !== $data && array_key_exists('url', $data)) {
                $nw->setUrl($data['url']);
            }

            $em->persist($nw);
        }

        $em->flush();
    }

    protected function createSitesForPublisher(PublisherInterface $publisher, array $siteData)
    {
        /**
         * @var EntityManagerInterface $em
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /**
         * @var SiteRepositoryInterface $siteRepository
         */
        $siteRepository = $this->getContainer()->get('tagcade.repository.site');

        foreach ($siteData as $name => $data) {
            $site = $siteRepository->findBy(array('name' => $name));
            if (!empty($site)) {
                continue;
            }

            $site = new Site();
            $site->setName($name);
            $site->setPublisher($publisher);

            if (!array_key_exists('domain', $data)) {
                throw new \Exception(sprintf('Expect domain for site %s', $name));
            }
            $site->setDomain($data['domain']);

            $site->setAutoCreate(false);
            $site->setEnableSourceReport(true); // default


            if (array_key_exists('adSlots', $data)) {
                $this->createAdSlotsForSite($site, $data['adSlots']);
            }

            $em->persist($site);

            $em->flush();
        }
    }

    protected function createAdSlotsForSite(SiteInterface $site, array $adSlots)
    {
        /**
         * @var EntityManagerInterface $em
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /**
         * @var LibraryAdSlotRepositoryInterface $libraryAdSlotRepository
         */
        $libraryAdSlotRepository = $this->getContainer()->get('tagcade.repository.library_ad_slot');

        foreach ($adSlots as $name => $data) {
            $slot = $libraryAdSlotRepository->findBy(array('name' => $name));
            if (!empty($slot)) {
                continue;
            }

            $type = isset($data['type']) ? $data['type'] : self::AD_SLOT_TYPE_DISPLAY;
            if ($type != self::AD_SLOT_TYPE_DISPLAY) {
                throw new \Exception(sprintf('Do not support ad slot type yet %s', $type));
            }

            if (!array_key_exists('width', $data) || !array_key_exists('height', $data)) {
                throw new \Exception(sprintf('Expect with and height for ad slot %s', $name));

            }

            $slot = new DisplayAdSlot();
            $slot->setSite($site);
            $slot->setAutoCreate(false);

            $libraryDisplayAdSlot = new LibraryDisplayAdSlot();
            $libraryDisplayAdSlot->setPublisher($site->getPublisher());
            $libraryDisplayAdSlot->setVisible(false);
            $libraryDisplayAdSlot->setPassbackMode('position');
            $libraryDisplayAdSlot->setAutoFit(false);
            $em->persist($libraryDisplayAdSlot);
            $slot->setLibraryAdSlot($libraryDisplayAdSlot);

            $slot->setName($name);
            $slot->setWidth($data['width']);
            $slot->setHeight($data['height']);


            if (array_key_exists('adTags', $data)) {
                $this->createAdTagsForAdSlot($slot, $data['adTags']);
            }

            $em->persist($slot);

            $site->getAllAdSlots()->add($slot);
        }
    }

    protected function createAdTagsForAdSlot(ReportableAdSlotInterface $adSlot, array $adTags)
    {
        /**
         * @var EntityManagerInterface $em
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /**
         * @var LibraryAdTagRepositoryInterface $libraryAdTagRepository
         */
        $libraryAdTagRepository = $this->getContainer()->get('tagcade.repository.library_ad_tag');

        /**
         * @var AdNetworkRepositoryInterface $adNetworkRepository
         */
        $adNetworkRepository = $this->getContainer()->get('tagcade.repository.ad_network');

        foreach ($adTags as $name => $data) {
            $libAdTags = $libraryAdTagRepository->findBy(array('name' => $name));
            if (!empty($libAdTags)) {
                continue;
            }

            if (!array_key_exists('adNetwork', $data)) {
                throw new \Exception(sprintf('Expect "adNetwork" property for ad tag %s', $name));
            }

            $adNetworkName = $data['adNetwork'];
            $adNetworks = $adNetworkRepository->findBy(array('name' => $adNetworkName));
            if (empty($adNetworks)) {
                throw new \Exception(sprintf('Cannot create ad tag %s due to ad network %s is not found', $name, $adNetworkName));
            }

            $adNetwork = current($adNetworks);
            if (!$adNetwork instanceof AdNetworkInterface) {
                throw new \Exception(sprintf('Cannot create ad tag %s due to ad network %s is not found', $name, $adNetworkName));
            }

            $adTag = new AdTag();

            $libraryAdTag = new LibraryAdTag();
            $libraryAdTag->setVisible(false);
            $libraryAdTag->setAdType(0);
            $em->persist($libraryAdTag);
            $adTag->setLibraryAdTag($libraryAdTag);

            $adTag->setName($name);
            $adTag->setAdSlot($adSlot);
            $adTag->setAdNetwork($adNetwork);
            $adTag->setActive(true);
            $adTag->setRefId(uniqid('', true));

            if (!array_key_exists('html', $data)) {
                throw new \Exception(sprintf('Expect "html" property for ad tag %s', $name));
            }
            $adTag->setHtml($data['html']);

            $em->persist($adTag);

            $adSlot->getAdTags()->add($adTag);
        }
    }


    protected function createReportsForPublisher(PublisherInterface $publisher, array $reportData)
    {
        if (array_key_exists('performance', $reportData)) {
            $this->createPerformanceReportsForPublisher($publisher, $reportData['performance']);
        }

        if (array_key_exists('source', $reportData)) {
            $this->createSourceReportsForPublisher($publisher, $reportData['source']);
        }
    }

    protected function createPerformanceReportsForPublisher(PublisherInterface $publisher, array $reportData)
    {
        if (!array_key_exists('startDate', $reportData) || !array_key_exists('endDate', $reportData)) {
            throw new \Exception(sprintf('Expect start date and end date to create report for publisher %d', $publisher->getId()));
        }

        $startDate = new \DateTime($reportData['startDate']);
        $endDate = new \DateTime($reportData['endDate']);

        $this->validateReportDateRange($startDate, $endDate);

        $command = $this->getApplication()->find(self::GENERATE_PERFORMANCE_REPORT_COMMAND);
        $arguments = array(
            'command' => self::GENERATE_PERFORMANCE_REPORT_COMMAND,
            '--publisher' => $publisher->getId(),
            '--startDate' => $reportData['startDate'],
            '--endDate' => $reportData['endDate'],

        );

        $input = new ArrayInput($arguments);
        $output = new BufferedOutput();

        $command->run($input, $output);
    }

    protected function createSourceReportsForPublisher(PublisherInterface $publisher, array $reportData)
    {
        if (!array_key_exists('startDate', $reportData) || !array_key_exists('endDate', $reportData)) {
            throw new \Exception(sprintf('Expect start date and end date to create report for publisher %d', $publisher->getId()));
        }

        $startDate = new \DateTime($reportData['startDate']);
        $endDate = new \DateTime($reportData['endDate']);

        $this->validateReportDateRange($startDate, $endDate);

        $command = $this->getApplication()->find(self::GENERATE_SOURCE_REPORT_COMMAND);
        $arguments = array(
            'command' => self::GENERATE_SOURCE_REPORT_COMMAND,
            '--publisher' => $publisher->getId(),
            '--startDate' => $reportData['startDate'],
            '--endDate' => $reportData['endDate'],

        );

        $input = new ArrayInput($arguments);
        $output = new BufferedOutput();

        $command->run($input, $output);
    }

    protected function validateReportDateRange(\DateTime $startDate, \DateTime $endDate)
    {
        $today = new \DateTime('today');

        if ($startDate > $endDate || $endDate >= $today) {
            throw new \Exception('startDate should not exceed endDate and endDate should not exceed yesterday');
        }
    }
} 