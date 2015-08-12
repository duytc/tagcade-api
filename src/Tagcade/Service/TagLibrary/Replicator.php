<?php

namespace Tagcade\Service\TagLibrary;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\Expression;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;

class Replicator implements ReplicatorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ChecksumValidatorInterface
     */
    private $checksumValidator;
    /**
     * @var AdSlotGeneratorInterface
     */
    private $adSlotGenerator;
    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;

    function __construct(EntityManagerInterface $em, ChecksumValidatorInterface $checksumValidator)
    {
        $this->em = $em;
        $this->checksumValidator = $checksumValidator;
    }

    public function setAdSlotGenerator(AdSlotGeneratorInterface $adSlotGenerator) {
        $this->adSlotGenerator = $adSlotGenerator;
    }

    public function setAdSlotManager(AdSlotManagerInterface $adSlotManager) {
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * Replicate all ad tags of a shared ad slot to other ad slots that refer to the same library
     * @param $libAdSlot
     * @param $adSlot
     *
     * @return BaseAdSlotInterface
     */
    public function replicateFromLibrarySlotToSingleAdSlot(BaseLibraryAdSlotInterface $libAdSlot, BaseAdSlotInterface &$adSlot)
    {
        if(!$libAdSlot instanceof LibraryDisplayAdSlotInterface && !$libAdSlot instanceof LibraryNativeAdSlotInterface) {
            throw new InvalidArgumentException('expect instance of LibraryDisplayAdSlotInterface or LibraryNativeAdSlotInterface');
        }

        if(!$adSlot instanceof DisplayAdSlotInterface && !$adSlot instanceof NativeAdSlotInterface) {
            throw new InvalidArgumentException('expect instance of DisplayAdSlotInterface or NativeAdSlotInterface');
        }

        if(null !== $adSlot->getId() && $adSlot->getLibraryAdSlot()->getId() === $libAdSlot->getId()) {
            return $adSlot; // nothing to be replicated
        }

        $this->em->getConnection()->beginTransaction();

        try {
            // add new ad slot that refers to a library then we have to replicate all tags in that library to the slot
            $librarySlotTags = $libAdSlot->getLibSlotTags();

            $adTags = $adSlot->getAdTags();
            foreach($adTags as $t) {
                $this->em->remove($t);
            }

            $adSlot->getAdTags()->clear();

            foreach($librarySlotTags as $librarySlotTag) {
                $newAdTag = new AdTag();
                $newAdTag->setAdSlot($adSlot);
                $newAdTag->setRefId($librarySlotTag->getRefId());
                $newAdTag->setLibraryAdTag($librarySlotTag->getLibraryAdTag());
                $newAdTag->setFrequencyCap($librarySlotTag->getFrequencyCap());
                $newAdTag->setPosition($librarySlotTag->getPosition());
                $newAdTag->setRotation($librarySlotTag->getRotation());
                $newAdTag->setActive($librarySlotTag->isActive());

                $adSlot->getAdTags()->add($newAdTag);
            }

            $this->em->persist($adSlot);
            $this->em->flush();

            $this->checksumValidator->validateAdSlotSynchronization($adSlot);

            $this->em->getConnection()->commit();
        }
        catch(\Exception $ex) {
            $this->em->getConnection()->rollback();
            throw new RuntimeException($ex);
        }

        return $adSlot;
    }

    /**
     * add new ad tag to all slots that refer to the same library on persisting new $librarySlotTag
     * @param LibrarySlotTagInterface $librarySlotTag
     * @return AdTagInterface[]|null
     */
    public function replicateNewLibrarySlotTagToAllReferencedAdSlots(LibrarySlotTagInterface $librarySlotTag)
    {
        //check if the Library Slot has been referred by any Slot
        $adSlots = $librarySlotTag->getLibraryAdSlot()->getAdSlots();
        if(null === $adSlots) return null; // no slot refers to this library

        if($adSlots instanceof PersistentCollection) $adSlots = $adSlots->toArray();

        $createdAdTags = [];

        $this->em->getConnection()->beginTransaction();

        try {
            /**
             * @var BaseAdSlotInterface $adSlot
             */
            foreach($adSlots as $adSlot)
            {
                $newAdTag = new AdTag();
                $newAdTag->setAdSlot($adSlot);
                $newAdTag->setRefId($librarySlotTag->getRefId());
                $newAdTag->setLibraryAdTag($librarySlotTag->getLibraryAdTag());
                $newAdTag->setFrequencyCap($librarySlotTag->getFrequencyCap());
                $newAdTag->setPosition($librarySlotTag->getPosition());
                $newAdTag->setRotation($librarySlotTag->getRotation());
                $newAdTag->setActive($librarySlotTag->isActive());

                $adSlot->getAdTags()->add($newAdTag);
                $this->em->persist($adSlot);

                $createdAdTags[] = $newAdTag;
            }

            $this->em->flush();

            $this->checksumValidator->validateAllAdSlotsSynchronized($adSlots);

            $this->em->getConnection()->commit();

        }
        catch(\Exception $ex) {
            $this->em->getConnection()->rollback();
            throw new RuntimeException($ex);
        }

        return $createdAdTags;
    }

    /**
     * @param LibrarySlotTagInterface $librarySlotTag
     * @param bool $remove true if we are removing $librarySlotTag
     */
    public function replicateExistingLibrarySlotTagToAllReferencedAdTags(LibrarySlotTagInterface $librarySlotTag, $remove = false)
    {
        $siblingTags = $librarySlotTag->getLibraryAdTag()->getAdTags()->toArray();

        $adTags = array_filter($siblingTags, function(AdTagInterface $adTag) use($librarySlotTag, $librarySlotTag){
                return $adTag->getAdSlot()->getLibraryAdSlot()->getId() == $librarySlotTag->getLibraryAdSlot()->getId() && $librarySlotTag->getRefId() == $adTag->getRefId();
            });

        $this->em->getConnection()->beginTransaction();

        try {
            array_walk(
                $adTags,
                function(AdTagInterface $t) use($librarySlotTag, $remove){

                    if (true === $remove) {
                        $this->em->remove($t);
                        return;
                    }

                    $t->setLibraryAdTag($librarySlotTag->getLibraryAdTag());
                    $t->setFrequencyCap($librarySlotTag->getFrequencyCap());
                    $t->setPosition($librarySlotTag->getPosition());
                    $t->setRotation($librarySlotTag->getRotation());
                    $t->setActive($librarySlotTag->isActive());

                    $this->em->persist($t);
                }
            );

            $this->em->flush();
            
            $this->checksumValidator->validateAllAdSlotsSynchronized($librarySlotTag->getLibraryAdSlot()->getAdSlots()->toArray());

            $this->em->getConnection()->commit();
        }
        catch(\Exception $ex) {
            $this->em->getConnection()->rollback();

            throw new RuntimeException($ex);
        }
    }

    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @return mixed
     */
    public function replicateLibraryExpressionForAllReferencedDynamicAdSlots(
        LibraryExpressionInterface $libraryExpression
    ) {
        $libraryDynamicAdSlot = $libraryExpression->getLibraryDynamicAdSlot();
        $dynamicAdSlots = $libraryDynamicAdSlot->getAdSlots()->toArray();

        $this->em->getConnection()->beginTransaction();

        try {
            // adding or editing
            $processedSites = [];
            foreach ($dynamicAdSlots as $adSlot) {
                /**
                 * @var DynamicAdSlotInterface $adSlot
                 */
                $site = $adSlot->getSite();
                if (!in_array($site, $processedSites)) {
                    $this->adSlotGenerator->generateTrueDefaultAdSlotAndExpressionsForLibraryDynamicAdSlotBySite($libraryDynamicAdSlot, $site);
                }

                $processedSites[] = $site;

                // find existing expression use the same library expression
                $existingExpressions = array_filter($adSlot->getExpressions()->toArray(), function(ExpressionInterface $expression) use($libraryExpression) {
                        return $expression->getLibraryExpression()->getId() === $libraryExpression->getId();
                });

                $expectAdSlot = $this->adSlotManager->getReferencedAdSlotsForSite($libraryExpression->getExpectLibraryAdSlot(), $site);
                $expression = (null === $existingExpressions || count($existingExpressions) < 1) ? new Expression() : current($existingExpressions);

                $expression->setExpectAdSlot($expectAdSlot);
                $expression->setLibraryExpression($libraryExpression);
                $expression->setDynamicAdSlot($adSlot);

                if (!in_array($expression, $existingExpressions)) { // for the case add new expression
                    $adSlot->getExpressions()->add($expression);
                }

                $this->em->persist($expression);
                $this->em->persist($adSlot);
            }

            unset($processedSites);

            $this->em->flush();

            $this->em->getConnection()->commit();
        }
        catch(\Exception $e) {
            $this->em->getConnection()->rollback();

            throw new RuntimeException($e);
        }
    }

    /**
     * Replicate multiple library expressions for all referenced dynamic ad slots
     *
     * @param array $libraryExpressions
     * @return mixed
     */
    public function replicateLibraryExpressionsForAllReferencedDynamicAdSlots(array $libraryExpressions)
    {
        foreach ($libraryExpressions as $libraryExpression) {
            $this->replicateLibraryExpressionForAllReferencedDynamicAdSlots($libraryExpression);
        }
    }
}