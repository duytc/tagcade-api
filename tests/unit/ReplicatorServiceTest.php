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
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Repository\Core\DisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\DynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryAdTagRepositoryInterface;
use Tagcade\Repository\Core\LibraryDisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class ReplicatorServiceTest extends \Codeception\TestCase\Test
{
    /** @var \UnitTester */
    protected $tester;

    /** @var ReplicatorInterface */
    protected $replicator;

    /** @var EntityManagerInterface */
    protected $em;

    protected function _before()
    {
        $this->replicator = $this->tester->grabServiceFromContainer('tagcade_api.service.tag_library.replicator');
        $this->em = $this->tester->grabServiceFromContainer('doctrine')->getManager();
    }

    protected function _after()
    {
    }


    /**
     * @test
     *
     * Run steps:
     * 1. fetching an ad slot
     * 2. fetching an new library ad slot
     * 3. replicate the new library ad slot to the above ad slot
     * 4. assert that some ad tags get created and some removed
     */
    public function replicateFromLibrarySlotToSingleAdSlot()
    {
        /**@var DisplayAdSlotRepositoryInterface $displayAdSlotRepository */
        $displayAdSlotRepository =  $this->em->getRepository(\Tagcade\Entity\Core\DisplayAdSlot::class);
        /**@var AdTagRepositoryInterface $adTagRepository */
        $adTagRepository = $this->em->getRepository(\Tagcade\Entity\Core\AdTag::class);

        /** @var DisplayAdSlotInterface $adSlot */
        // $adSlot 1 has 2 ad tags
        $adSlot = $displayAdSlotRepository->findOneBy(array('id' => 1));
        $oldLibraryAdSlot = $adSlot->getLibraryAdSlot();

        $this->tester->assertNotNull($oldLibraryAdSlot);
        $this->tester->assertEquals(2, count($oldLibraryAdSlot->getLibSlotTags()->toArray()));
        $this->tester->assertNotNull($adSlot);
        $this->tester->assertEquals(2, count($adSlot->getAdTags()->toArray()));

        /** @var LibraryDisplayAdSlotRepositoryInterface $libraryDisplayAdSlotRepository */
        $libraryDisplayAdSlotRepository = $this->em->getRepository(\Tagcade\Entity\Core\LibraryDisplayAdSlot::class);

        /**@var LibraryDisplayAdSlotInterface $newLibraryAdSlot */
        // $newLibraryAdSlot 2 has 3 ad tags
        $newLibraryAdSlot = $libraryDisplayAdSlotRepository->findOneBy(array('id' => 2));

        $this->assertNotNull($newLibraryAdSlot);
        $this->tester->assertEquals(3, count($newLibraryAdSlot->getLibSlotTags()->toArray()));

        // Start replicating
        $this->replicator->replicateFromLibrarySlotToSingleAdSlot($newLibraryAdSlot, $adSlot);

        $adTag1 = $adTagRepository->findBy(array('libraryAdTag' => 1));
        $adTag2 = $adTagRepository->findBy(array('libraryAdTag' => 2));
        $adTag3 = $adTagRepository->findBy(array('libraryAdTag' => 3));
        $adTag4 = $adTagRepository->findBy(array('libraryAdTag' => 4));
        $adTag5 = $adTagRepository->findBy(array('libraryAdTag' => 5));

        // $adTag1 and $adTag2 should be removed
        // $adTag3, $adTag4 and $adTag5 should be created new
        $this->assertCount(0, $adTag1);
        $this->assertCount(0, $adTag2);
        $this->assertCount(1, $adTag3);
        $this->assertCount(1, $adTag4);
        $this->assertCount(1, $adTag5);
        $this->assertEquals('Ad Tag 3', $adTag3[0]->getName());
        $this->assertEquals('Ad Tag 4', $adTag4[0]->getName());
        $this->assertEquals('Ad Tag 5', $adTag5[0]->getName());

        $this->tester->assertEquals(3, count($adSlot->getAdTags()->toArray()));
    }

    /**
     * @test
     *
     * Run steps:
     * 1. fetching an ad slot
     * 2. fetching an new library ad slot
     * 3. replicate the new library ad slot to the above ad slot
     * 4. assert that some ad tags get created and some removed
     */
    public function switchingLibraryOfDisplayAdSlotReferredByDynamicAdSlot()
    {
        /**@var DisplayAdSlotRepositoryInterface $displayAdSlotRepository */
        $displayAdSlotRepository =  $this->em->getRepository(\Tagcade\Entity\Core\DisplayAdSlot::class);
        /**@var DynamicAdSlotRepositoryInterface $dynamicAdSlotRepository */
        $dynamicAdSlotRepository =  $this->em->getRepository(\Tagcade\Entity\Core\DynamicAdSlot::class);

        /** @var DisplayAdSlotInterface $adSlot */
        // $adSlot 5 has 3 ad tags
        $adSlot = $displayAdSlotRepository->findOneBy(array('id' => 2));
        $oldLibraryAdSlot = $adSlot->getLibraryAdSlot();

        $this->tester->assertNotNull($oldLibraryAdSlot);
        $this->tester->assertEquals(3, count($oldLibraryAdSlot->getLibSlotTags()->toArray()));
        $this->tester->assertNotNull($adSlot);
        $this->tester->assertEquals(3, count($adSlot->getAdTags()->toArray()));

        /** @var LibraryDisplayAdSlotRepositoryInterface $libraryDisplayAdSlotRepository */
        $libraryDisplayAdSlotRepository = $this->em->getRepository(\Tagcade\Entity\Core\LibraryDisplayAdSlot::class);

        /**@var LibraryDisplayAdSlotInterface $newLibraryAdSlot */
        // $newLibraryAdSlot 1 has 2 ad tags
        $newLibraryAdSlot = $libraryDisplayAdSlotRepository->findOneBy(array('id' => 8));

        $this->assertNotNull($newLibraryAdSlot);
        $this->tester->assertEquals(2, count($newLibraryAdSlot->getLibSlotTags()->toArray()));

        /**
         * @var DynamicAdSlotInterface $dynamicAdSlot
         */
        $dynamicAdSlot = $dynamicAdSlotRepository->findOneBy(array('id' => 3));
        $expressions = $dynamicAdSlot->getExpressions()->toArray();
        // all starting position not null
        /** @var ExpressionInterface $expression */
        foreach($expressions as $expression) {
            $this->assertNotNull($expression->getStartingPosition());
        }

        // Start replicating
        $this->replicator->replicateFromLibrarySlotToSingleAdSlot($newLibraryAdSlot, $adSlot);

        $startingPositionNull = false;
        /** @var ExpressionInterface $expression */
        foreach($expressions as $expression) {
            if($expression->getStartingPosition() === null) {
                $startingPositionNull = true;
                break;
            }
        }

        // there should be one expression get starting position null
        $this->assertTrue($startingPositionNull);
    }

    /**
     * @test
     *
     * Run steps:
     * 1. fetching an ad slot
     * 2. fetching an new library ad slot
     * 3. replicate the new library ad slot to the above ad slot
     * 4. assert that some ad tags get created and some removed
     */
    public function switchingLibraryDynamicAdSlot()
    {
        // TODO prevent this to be taken place
        /**@var LibraryDynamicAdSlotRepositoryInterface $libraryDynamicAdSlotRepository */
        $libraryDynamicAdSlotRepository =  $this->em->getRepository(\Tagcade\Entity\Core\LibraryDynamicAdSlot::class);
        /**@var DynamicAdSlotRepositoryInterface $dynamicAdSlotRepository */
        $dynamicAdSlotRepository =  $this->em->getRepository(\Tagcade\Entity\Core\DynamicAdSlot::class);

        /** @var DynamicAdSlotInterface $dynamicAdSlot */
        // $adSlot 6 has 3 ad expression
        $dynamicAdSlot = $dynamicAdSlotRepository->findOneBy(array('id' => 5));
        $oldLibraryAdSlot = $dynamicAdSlot->getLibraryAdSlot();

        $this->tester->assertNotNull($oldLibraryAdSlot);
        $this->tester->assertEquals(3, count($oldLibraryAdSlot->getLibraryExpressions()->toArray()));
        $this->tester->assertNotNull($dynamicAdSlot);
        $this->tester->assertEquals(3, count($dynamicAdSlot->getExpressions()->toArray()));


        /**@var LibraryDynamicAdSlotInterface $newLibraryAdSlot */
        // $newLibraryAdSlot 1 has 2 expressions
        $newLibraryAdSlot = $libraryDynamicAdSlotRepository->findOneBy(array('id' => 7));

        $this->assertNotNull($newLibraryAdSlot);
        $this->tester->assertEquals(2, count($newLibraryAdSlot->getLibraryExpressions()->toArray()));

        $dynamicAdSlot->setLibraryAdSlot($newLibraryAdSlot);

        $this->tester->persistEntity($dynamicAdSlot);
        $this->tester->flushToDatabase();

        $this->replicator->replicateLibraryDynamicAdSlotForAllReferencedDynamicAdSlots($newLibraryAdSlot);

        $this->assertCount(2, $dynamicAdSlot->getExpressions()->toArray());
    }

    /**
     * @test
     *
     * Run steps:
     * 1. fetching a library display ad slot
     * 2. creating a new library slot tag
     * 3. adding the created library slot tag to the library display ad slot above
     * 4. assert that all referred ad slot have a new created ad tag
     */
    public function replicateNewLibrarySlotTagToAllReferencedAdSlots()
    {
        /**@var DisplayAdSlotRepositoryInterface $displayAdSlotRepository */
        $displayAdSlotRepository =  $this->em->getRepository(\Tagcade\Entity\Core\DisplayAdSlot::class);
        /** @var LibraryDisplayAdSlotRepositoryInterface $libraryDisplayAdSlotRepository */
        $libraryDisplayAdSlotRepository = $this->em->getRepository(\Tagcade\Entity\Core\LibraryDisplayAdSlot::class);
        /** @var LibraryAdTagRepositoryInterface $libraryAdTagRepository */
        $libraryAdTagRepository = $this->em->getRepository(\Tagcade\Entity\Core\LibraryAdTag::class);

        /**@var LibraryDisplayAdSlotInterface $libraryAdSlot */
        // $libraryAdSlot has 2 ad tags
        $libraryAdSlot = $libraryDisplayAdSlotRepository->findOneBy(array('id' => 1));
        $this->tester->assertNotNull($libraryAdSlot);

        /**@var LibraryAdTagInterface $libraryAdTag */
        $libraryAdTag = $libraryAdTagRepository->findOneBy(array('id' => 3));
        $this->tester->assertNotNull($libraryAdTag);

        // add new library slot tag to $libraryAdSlot
        $librarySlotTag = new \Tagcade\Entity\Core\LibrarySlotTag();
        $librarySlotTag->setLibraryAdSlot($libraryAdSlot);
        $librarySlotTag->setLibraryAdTag($libraryAdTag);
        $librarySlotTag->setActive(true);
        $librarySlotTag->setRefId(uniqid('', true));

        $this->tester->persistEntity($librarySlotTag);
        $this->tester->flushToDatabase();

        // now $libraryAdSlot has 3 ad tags
        $this->replicator->replicateNewLibrarySlotTagToAllReferencedAdSlots($librarySlotTag);

        // $adSlot1 and $adSlot3 both referred to $libraryAdSlot
        $adSlot1 = $displayAdSlotRepository->findOneBy(array('id' => 1));
        $adSlot3 = $displayAdSlotRepository->findOneBy(array('id' => 2));

        // $adSlot1 and $adSlot3 should both have 3 ad tags
        $this->assertNotNull($adSlot1);
        $this->assertNotNull($adSlot3);
        $this->assertEquals(3, count($adSlot1->getAdTags()->toArray()));
        $this->assertEquals(3, count($adSlot3->getAdTags()->toArray()));
    }

    /**
     * @test
     *
     * Run steps:
     * 1. fetching a library slot tag belonging to a library ad slot
     * 2. update some of its properties
     * 3. assert that all sibling ad tags get corresponding changes
     */
    public function replicateExistingLibrarySlotTagToAllReferencedAdTags()
    {
        /**@var LibrarySlotTagRepositoryInterface $librarySlotTagRepository */
        $librarySlotTagRepository =  $this->em->getRepository(\Tagcade\Entity\Core\LibrarySlotTag::class);
        /**@var AdTagRepositoryInterface $adTagRepository */
        $adTagRepository = $this->em->getRepository(\Tagcade\Entity\Core\AdTag::class);

        /**@var LibrarySlotTagInterface $librarySlotTag */
        // $librarySlotTag 2 is activated
        $librarySlotTag = $librarySlotTagRepository->findOneBy(array('id' => 3));

        $this->assertNotNull($librarySlotTag);
        // deactivate $librarySlotTag 2
        $librarySlotTag->setActive(false);

        // Start replicating
        $this->replicator->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag);

        $adTags = $adTagRepository->findBy(array('libraryAdTag' => 3, 'active' => true));
        $this->assertCount(0, $adTags);

        // all referred ad tags should be deactivated as well
        /** @var AdTagInterface $adTag */
        foreach($adTags as $adTag) {
            $this->assertFalse($adTag->isActive());
        }
    }

    /**
     * @test
     *
     * Run steps:
     * 1. fetching a library dynamic ad slot
     * 2. adding new library expression to the library ad slot above
     * 3. assert that all referred dynamic ad slot get a new expression
     */
    public function replicateLibraryDynamicAdSlotForAllReferencedDynamicAdSlots()
    {
        /**@var DynamicAdSlotRepositoryInterface $dynamicAdSlotRepository */
        $dynamicAdSlotRepository =  $this->em->getRepository(\Tagcade\Entity\Core\DynamicAdSlot::class);
        /**@var LibraryDisplayAdSlotRepositoryInterface $libraryDisplayAdSlotRepository */
        $libraryDisplayAdSlotRepository = $this->em->getRepository(\Tagcade\Entity\Core\LibraryDisplayAdSlot::class);
        /**@var LibraryExpressionRepositoryInterface $libraryExpressionRepository */
        $libraryExpressionRepository = $this->em->getRepository(\Tagcade\Entity\Core\LibraryExpression::class);

        /** @var DynamicAdSlotInterface $dynamicAdSLot */
        // $dynamicAdSLot has 2 expression
        /** @var DynamicAdSlotInterface $dynamicAdSLot */
        $dynamicAdSLot = $dynamicAdSlotRepository->findOneBy(array('id' => 5));
        $this->assertNotNull($dynamicAdSLot);
        // $dynamicAdSLot1 has 2 expression
        /** @var DynamicAdSlotInterface $dynamicAdSLot1 */
        $dynamicAdSLot1 = $dynamicAdSlotRepository->findOneBy(array('id' => 7));
        $this->assertNotNull($dynamicAdSLot1);

        $expressions = $dynamicAdSLot->getExpressions()->toArray();
        $this->assertEquals(3, count($expressions));

        /**@var LibraryDynamicAdSlotInterface $libraryDynamicAdSlot */
        $libraryDynamicAdSlot = $dynamicAdSLot->getLibraryAdSlot();
        $this->assertNotNull($libraryDynamicAdSlot);
        $libraryExpressions = $libraryDynamicAdSlot->getLibraryExpressions()->toArray();
        $this->assertEquals(3, count($libraryExpressions));

        /** @var LibraryDisplayAdSlotInterface $expectLibraryAdSlot */
        $expectLibraryAdSlot = $libraryDisplayAdSlotRepository->findOneBy(array('id' => 1));
        $this->assertNotNull($expectLibraryAdSlot);

        /**
         * @var LibraryExpressionInterface $libraryExpression
         */
        $libraryExpression = $libraryExpressionRepository->findOneBy(array('id' => 1));
        $this->assertNotNull($libraryExpression);

        $newLibraryExpression = clone $libraryExpression;
        $newLibraryExpression->setId(null);
        $newLibraryExpression->setStartingPosition(2);
        $newLibraryExpression->setExpectLibraryAdSlot($expectLibraryAdSlot);

        $libraryDynamicAdSlot->getLibraryExpressions()->add($newLibraryExpression);
        $this->tester->persistEntity($libraryDynamicAdSlot);
        $this->tester->flushToDatabase();

        $this->replicator->replicateLibraryDynamicAdSlotForAllReferencedDynamicAdSlots($libraryDynamicAdSlot);

        // expect $dynamicAdSLot and $dynamicAdSLot1 has 3 expression
        $this->assertEquals(4, count($dynamicAdSLot->getExpressions()->toArray()));
        $this->assertEquals(4, count($dynamicAdSLot1->getExpressions()->toArray()));
    }

}