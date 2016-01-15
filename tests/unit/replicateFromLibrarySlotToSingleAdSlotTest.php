<?php


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class replicateFromLibrarySlotToSingleAdSlotTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var ReplicatorInterface
     */
    protected $replicator;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    protected function _before()
    {
        $this->replicator = $this->tester->grabServiceFromContainer('tagcade_api.service.tag_library.replicator');

        $this->em = $this->tester->grabServiceFromContainer('doctrine')->getManager();
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
        /**
         * @var SiteRepositoryInterface $siteRepository
         */
        $siteRepository = $this->em->getRepository(\Tagcade\Entity\Core\Site::class);
        $site = $siteRepository->find(1);

        /**
         * @var AdNetworkRepositoryInterface $adNetworkRepository
         */
        $adNetworkRepository = $this->em->getRepository(\Tagcade\Entity\Core\AdNetwork::class);
        $adNetwork = $adNetworkRepository->find(1);

        // create new library ad slot
        $libraryAdSLot = new \Tagcade\Entity\Core\LibraryDisplayAdSlot();
        $libraryAdSLot->setName("Giang Test");
        $libraryAdSLot->setHeight(200);
        $libraryAdSLot->setWidth(600);
        $libraryAdSLot->setType("display");
        $libraryAdSLot->setVisible(true);
        $libraryAdSLot->setAutoFit(true);
        $libraryAdSLot->setPassbackMode('position');

        $this->tester->persistEntity($libraryAdSLot);

        // create new ad slot
        $adSlot = new \Tagcade\Entity\Core\DisplayAdSlot();
        $adSlot->setLibraryAdSlot($libraryAdSLot);
        $adSlot->setSite($site);

        $this->tester->persistEntity($adSlot);


        // add new ad tags to the ad slot
        $libraryAdTag = new \Tagcade\Entity\Core\LibraryAdTag();
        $libraryAdTag->setName("TAG 1");
        $libraryAdTag->setAdType(0);
        $libraryAdTag->setHtml("TAG 1");
        $libraryAdTag->setAdNetwork($adNetwork);
        $libraryAdTag->setVisible(true);

        $adTag = new \Tagcade\Entity\Core\AdTag();
        $adTag->setLibraryAdTag($libraryAdTag);
        $adTag->setAdSlot($adSlot);
        $adTag->setActive(true);
        $adTag->setRefId(uniqid('', true));


        $this->tester->persistEntity($libraryAdTag);
        $this->tester->persistEntity($adTag);

        $libraryAdTag = new \Tagcade\Entity\Core\LibraryAdTag();
        $libraryAdTag->setName("TAG 2");
        $libraryAdTag->setAdType(0);
        $libraryAdTag->setHtml("TAG 2");
        $libraryAdTag->setAdNetwork($adNetwork);
        $libraryAdTag->setVisible(true);

        $adTag = new \Tagcade\Entity\Core\AdTag();
        $adTag->setLibraryAdTag($libraryAdTag);
        $adTag->setAdSlot($adSlot);
        $adTag->setActive(true);
        $adTag->setRefId(uniqid('', true));

        $this->tester->persistEntity($libraryAdTag);
        $this->tester->persistEntity($adTag);

        $libraryAdTag = new \Tagcade\Entity\Core\LibraryAdTag();
        $libraryAdTag->setName("TAG 3");
        $libraryAdTag->setAdType(0);
        $libraryAdTag->setHtml("TAG 3");
        $libraryAdTag->setAdNetwork($adNetwork);
        $libraryAdTag->setVisible(true);

        $adTag = new \Tagcade\Entity\Core\AdTag();
        $adTag->setLibraryAdTag($libraryAdTag);
        $adTag->setAdSlot($adSlot);
        $adTag->setActive(true);
        $adTag->setRefId(uniqid('', true));

        $this->tester->persistEntity($libraryAdTag);
        $this->tester->persistEntity($adTag);

        $this->tester->flushToDatabase();

        $this->tester->assertEquals(count($adSlot->getAdTags()), 3);
    }

}