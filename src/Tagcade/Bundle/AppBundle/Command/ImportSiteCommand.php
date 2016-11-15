<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\LibraryNativeAdSlot;
use Tagcade\Entity\Core\NativeAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

class ImportSiteCommand extends ContainerAwareCommand
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    private $container;

    private $publisher;

    /**
     * @var SiteInterface[]
     */
    private $addedSites = [];
    /**
     * @var AdNetworkInterface[];
     */
    private $addedAdNetworks = [];

    /**
     * @var DisplayAdSlotInterface[]
     */
    private $addedAdSlots = [];

    /**
     * @var LibraryDisplayAdSlotInterface[]
     */
    private $addedLibraryAdSlot = [];

    /**
     * @var LibraryAdTagInterface[]
     */
    private $addedLibraryAdTag = [];

    protected function configure()
    {
        $this
            ->setName('tc:site:import')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'path to file to be imported')
            ->addOption('publisher', 'p', InputOption::VALUE_REQUIRED, 'publisher add data')
            ->setDescription('import site json to database ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getOption('file');
        $publisherId = $input->getOption('publisher');

        $this->container = $this->getContainer();
        $this->serializer = $this->container->get('serializer');

        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->publisher = $this->container->get('tagcade_user.domain_manager.publisher')->find($publisherId);

        $dir = $this->container->getParameter('kernel.root_dir');
        $rootDir = rtrim($dir, '/app');
        if (strpos($file, '/') != 0) { // relative path
            $file = ltrim($file, './');
            $file = sprintf('%s/%s', $rootDir, $file);
        }

        if(!is_file($file)) {
            throw new \Exception(sprintf('The specified file in not found or not accessible %s', $file));
        }

        $jsonAdTags = json_decode(file_get_contents($file));

        foreach($jsonAdTags as $jsonAdTag) {
            $adSlot = $this->createAdSlot($jsonAdTag->adSlot);
            $adNetwork = $this->createAdNetwork($jsonAdTag->libraryAdTag->adNetwork);
            $adTagLibraryFind = count($this->addedLibraryAdTag) && !!$this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id];

            if($adTagLibraryFind == false) {
                $this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id] = new LibraryAdTag();
                $this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id]->setName($jsonAdTag->libraryAdTag->name);
                $this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id]->setAdNetwork($adNetwork);
                $this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id]->setHtml($jsonAdTag->libraryAdTag->html);
                $this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id]->setVisible($jsonAdTag->libraryAdTag->visible);
                $this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id]->setAdType($jsonAdTag->libraryAdTag->adType);
                $this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id]->setDescriptor($jsonAdTag->libraryAdTag->descriptor);

                $this->em->persist($this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id]);
            }

            $adTag = new AdTag();
            $adTag->setRefId(uniqid('', true));
            $adTag->setPosition($jsonAdTag->position);
            $adTag->setActive($jsonAdTag->active);
            $adTag->setLibraryAdTag($this->addedLibraryAdTag[$jsonAdTag->libraryAdTag->id]);
            $adTag->setAdSlot($adSlot);
            $adTag->setCheckSum();

            $this->em->persist($adTag);
        }

        $this->em->flush();
    }

    private function createAdSlot($adSlot) {
        $adSlotFind = count($this->addedAdSlots) > 0 && !!$this->addedAdSlots[$adSlot->id];

        if($adSlotFind == false) {
            $site = $this->createSite($adSlot->site);
            $adSlotLibraryFind = count($this->addedLibraryAdSlot) > 0 && !!$this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id];
            $adSlotFind = count($this->addedAdSlots) > 0 && !!$this->addedAdSlots[$adSlot->id];

            if($adSlot->type == 'display') {
                if($adSlotLibraryFind == false) {
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id] = new LibraryDisplayAdSlot();
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setName($adSlot->libraryAdSlot->name);
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setHeight($adSlot->libraryAdSlot->height);
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setWidth($adSlot->libraryAdSlot->width);
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setAutoFit($adSlot->libraryAdSlot->autoFit);
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setPassbackMode($adSlot->libraryAdSlot->passbackMode);
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setType($adSlot->libraryAdSlot->type);
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setVisible($adSlot->libraryAdSlot->visible);

                    $this->em->persist($this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]);
                }

                if($adSlotFind == false) {
                    $this->addedAdSlots[$adSlot->id] = new DisplayAdSlot();
                    $this->addedAdSlots[$adSlot->id]->setLibraryDisplayAdSlot($this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]);
                    $this->addedAdSlots[$adSlot->id]->setRtbStatus($adSlot->rtbStatus);
                    $this->addedAdSlots[$adSlot->id]->setSlotType($adSlot->slot_type);
                    $this->addedAdSlots[$adSlot->id]->setSite($site);
                    $this->addedAdSlots[$adSlot->id]->setCheckSum();

                    $this->em->persist($this->addedAdSlots[$adSlot->id]);
                }

                return $this->addedAdSlots[$adSlot->id];
            }
            elseif($adSlot->type == 'native') {
                if($adSlotLibraryFind == false) {
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id] = new LibraryNativeAdSlot();
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setName($adSlot->libraryAdSlot->name);
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setType($adSlot->libraryAdSlot->type);
                    $this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]->setVisible($adSlot->libraryAdSlot->visible);

                    $this->em->persist($this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]);
                }

                if($adSlotFind == false) {
                    $this->addedAdSlots[$adSlot->id] = new NativeAdSlot();
                    $this->addedAdSlots[$adSlot->id]->setLibraryAdSlot($this->addedLibraryAdSlot[$adSlot->libraryAdSlot->id]);
                    $this->addedAdSlots[$adSlot->id]->setSlotType($adSlot->slot_type);
                    $this->addedAdSlots[$adSlot->id]->setSite($site);
                    $this->addedAdSlots[$adSlot->id]->setCheckSum();

                    $this->em->persist($this->addedAdSlots[$adSlot->id]);
                }

                return $this->addedAdSlots[$adSlot->id];
            }
        }

        return $this->addedAdSlots[$adSlot->id];
    }

    private function createSite($site) {
        $siteFind =  count($this->addedSites) && !!$this->addedSites[$site->id];

        if($siteFind == false) {
            $this->addedSites[$site->id] = new Site();
            $this->addedSites[$site->id]->setPublisher($this->publisher);
            $this->addedSites[$site->id]->setName($site->name);
            $this->addedSites[$site->id]->setDomain($site->domain);
            $this->addedSites[$site->id]->setRtbStatus($site->rtbStatus);
            $this->addedSites[$site->id]->setEnableSourceReport($site->enableSourceReport);
            $this->addedSites[$site->id]->setPlayers($site->players);
            $this->addedSites[$site->id]->setAutoCreate($site->autoCreate);

            $this->em->persist($this->addedSites[$site->id]);

            return $this->addedSites[$site->id];
        }

        return $this->addedSites[$site->id];
    }

    private function createAdNetwork($adNetwork) {
        $adNetworkFind =  count($this->addedAdNetworks) && !!$this->addedAdNetworks[$adNetwork->id];
        $adNetworkPartner = null;

        if(is_object($adNetwork->networkPartner)) {
            $adNetworkPartner = $this->container->get('tagcade.domain_manager.ad_network_partner')->getByCanonicalName($adNetwork->networkPartner->nameCanonical);

            if($adNetworkPartner instanceof AdNetworkPartnerInterface) {
                $adNetwork = $this->container->get('tagcade.repository.ad_network')->getAdNetworkByPublisherAndPartnerCName( $this->publisher, $adNetworkPartner->getNameCanonical());

                if($adNetwork instanceof AdNetworkInterface) {
                    return $adNetwork;
                }
            }
            else {
                $adNetworkPartner = new AdNetworkPartner();
                $adNetworkPartner->setUrl($adNetwork->networkPartner->url);
                $adNetworkPartner->setName($adNetwork->networkPartner->name);

                $this->em->persist($adNetworkPartner);
            }
        }

        if($adNetworkFind == false) {
            $this->addedAdNetworks[$adNetwork->id] = new AdNetwork();
            $this->addedAdNetworks[$adNetwork->id]->setPublisher($this->publisher);
            $this->addedAdNetworks[$adNetwork->id]->setName($adNetwork->name);
            $this->addedAdNetworks[$adNetwork->id]->setUrl($adNetwork->url);
            $this->addedAdNetworks[$adNetwork->id]->setNetworkPartner($adNetworkPartner);

            $this->em->persist($this->addedAdNetworks[$adNetwork->id]);

            return $this->addedAdNetworks[$adNetwork->id];
        }

        return $this->addedAdNetworks[$adNetwork->id];
    }
}