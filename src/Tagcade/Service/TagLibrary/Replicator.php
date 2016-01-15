<?php

namespace Tagcade\Service\TagLibrary;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\LibraryExpression;
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
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Repository\Core\DynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\ExpressionRepositoryInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;

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
     * Copy all ad tags from an library ad slot to the given ad slot. This function is only meaningful to
     * display ad slot or native ad slot
     *
     * @param $libAdSlot
     * @param $adSlot
     *
     * @return BaseAdSlotInterface
     */
    public function replicateFromLibrarySlotToSingleAdSlot(BaseLibraryAdSlotInterface $libAdSlot, BaseAdSlotInterface &$adSlot)
    {
        $referenceSlot = $this->adSlotManager->getReferencedAdSlotsForSite($libAdSlot, $adSlot->getSite());
        if ($referenceSlot instanceof DisplayAdSlotInterface && $referenceSlot->getId() !== $adSlot->getId()) {
            throw new LogicException('Cannot create more than one ad slots in the same site referring to the same library');
        }

        if(!$libAdSlot instanceof LibraryDisplayAdSlotInterface && !$libAdSlot instanceof LibraryNativeAdSlotInterface) {
            throw new InvalidArgumentException('expect instance of LibraryDisplayAdSlotInterface or LibraryNativeAdSlotInterface');
        }

        if(!$adSlot instanceof DisplayAdSlotInterface && !$adSlot instanceof NativeAdSlotInterface) {
            throw new InvalidArgumentException('expect instance of DisplayAdSlotInterface or NativeAdSlotInterface');
        }

        $oldLibrary = $adSlot->getLibraryAdSlot();

        if(null !== $adSlot->getId() && $oldLibrary->getId() === $libAdSlot->getId()) {
            return $adSlot; // nothing to be replicated
        }
        $this->em->getConnection()->beginTransaction();
        try {

            // Update library of the ad slot
            $adSlot->setLibraryAdSlot($libAdSlot);
            // add new ad slot that refers to a library then we have to replicate all tags in that library to the slot
            $librarySlotTags = $libAdSlot->getLibSlotTags();
            // remove old ad tags
            $adTags = $adSlot->getAdTags();
            foreach($adTags as $t) {
                $this->em->remove($t);
            }
            $adSlot->getAdTags()->clear();
            // add new ad tags
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

            // replicate for library expression in dynamic ad slot
            /** @var ExpressionRepositoryInterface $expressionRepository */
            $expressionRepository = $this->em->getRepository(Expression::class);
            $expressions = $expressionRepository->getByExpectAdSlot($adSlot);
            if(!empty($expressions)) {
                /** @var ExpressionInterface $expression */
                foreach($expressions as $expression) {
                    // replace with the new library ad slot
                    $libraryExpression = $expression->getLibraryExpression();
                    $libraryExpression->setExpectLibraryAdSlot($libAdSlot);
                    $this->em->merge($libraryExpression);
                }
            }

            // 2. Check if there are changes in default library ad slot
            /**
             * @var DynamicAdSlotRepositoryInterface $dynamicAdSlotRepository
             */
            $dynamicAdSlotRepository = $this->em->getRepository(DynamicAdSlot::class);
            $dynamicAdSlots = $dynamicAdSlotRepository->getDynamicAdSlotsThatHaveDefaultAdSlot($adSlot);
            if(!empty($dynamicAdSlots)) {
                /**
                 * @var DynamicAdSlotInterface $dynamicAdSlot
                 */
                foreach($dynamicAdSlots as $dynamicAdSlot) {
                    $libraryAdSlot = $dynamicAdSlot->getLibraryAdSlot();
                    $libraryAdSlot->setDefaultLibraryAdSlot($libraryAdSlot);
                    $this->em->merge($libraryAdSlot);
                }
            }

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
     * This function gets invoked when user add a new ad tag to a library ad slot
     *
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
     * Replicate changes from an library ad tag to all reference ad tags
     * @param LibrarySlotTagInterface $librarySlotTag
     * @param bool $remove true if we are removing $librarySlotTag
     */
    public function replicateExistingLibrarySlotTagToAllReferencedAdTags(LibrarySlotTagInterface $librarySlotTag, $remove = false)
    {
        $this->em->getConnection()->beginTransaction();

        try {
            /**
             * @var AdTagRepositoryInterface $adTagRepository
             */
            $adTagRepository = $this->em->getRepository(AdTag::class);
            $adTags = $adTagRepository->getAdTagsByLibraryAdSlotAndRefId($librarySlotTag->getLibraryAdSlot(), $librarySlotTag->getRefId());

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

            // if there no any more AdTag refer to this LibraryAdTag then it should be removed as well
            $libraryAdTag = $librarySlotTag->getLibraryAdTag();

            if(true === $remove &&
                $libraryAdTag->getAssociatedTagCount() < 1 &&
                count($libraryAdTag->getLibSlotTags()) < 2) {
                $this->em->remove($libraryAdTag);
            }

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
     * Replicate Default Library AdSlot and Expression to all referenced Dynamic AdSLot
     *
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @return mixed
     */
    public function replicateLibraryDynamicAdSlotForAllReferencedDynamicAdSlots(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot)
    {
        // affected dynamic ad slots
        $dynamicAdSlots = $libraryDynamicAdSlot->getAdSlots();

        if ($dynamicAdSlots == null || empty($dynamicAdSlots)) {
            return; // nothing to be replicated
        }

        $this->em->getConnection()->beginTransaction();
        try {
            // replicate default library ad slot
            $defaultLibraryAdSlot = $libraryDynamicAdSlot->getDefaultLibraryAdSlot();
            foreach ($dynamicAdSlots as $adSlot) {
                /**
                 * @var DynamicAdSlotInterface $adSlot
                 */
                $site = $adSlot->getSite();
                $this->adSlotGenerator->generateTrueDefaultAdSlotAndExpectAdSlotInExpressionsForLibraryDynamicAdSlotBySite($libraryDynamicAdSlot, $site);
                $currentReference = ($defaultLibraryAdSlot instanceof LibraryDisplayAdSlotInterface || $defaultLibraryAdSlot instanceof LibraryNativeAdSlotInterface) ? $this->adSlotManager->getReferencedAdSlotsForSite($defaultLibraryAdSlot, $site) : null;
                $adSlot->setDefaultAdSlot($currentReference);


                $libraryExpressions = $libraryDynamicAdSlot->getLibraryExpressions()->toArray(); // new library expression set
                // remove expressions of current slot not attaching to this new library expressions
                $tobeRemoveExpressions = array_filter(
                    $adSlot->getExpressions()->toArray(),
                    function(ExpressionInterface $exp) use ($libraryExpressions)
                    {
                        $newLibraryExpressions = $libraryExpressions !== null ? array_map(function(LibraryExpressionInterface $libExp) { return $libExp->getId();}, $libraryExpressions) : [];

                        return !in_array($exp->getLibraryExpression()->getId(), $newLibraryExpressions);
                    }
                );

                if (null !== $tobeRemoveExpressions && !empty($tobeRemoveExpressions)) {
                    array_walk($tobeRemoveExpressions, function (ExpressionInterface $exp) { $this->em->remove($exp); });
                }

                // updating or creating new expressions with respect to new library expressions
                if (!empty($libraryExpressions)) {
                    array_walk(
                        $libraryExpressions,
                        function(LibraryExpressionInterface $libraryExpression) use (&$adSlot)
                        {
                            // update with this new expressions
                            // find existing expression use the same library expression
                            $existingExpressionsToBeUpdated = array_filter($adSlot->getExpressions()->toArray(), function(ExpressionInterface $expression) use($libraryExpression) {
                                    return ($expression->getLibraryExpression()->getId() === $libraryExpression->getId() && null !== $libraryExpression->getId());
                                });

                            $expectAdSlot = $this->adSlotManager->getReferencedAdSlotsForSite($libraryExpression->getExpectLibraryAdSlot(), $adSlot->getSite());
                            $expression = (null === $existingExpressionsToBeUpdated || count($existingExpressionsToBeUpdated) < 1) ? new Expression() : current($existingExpressionsToBeUpdated);

                            $expression->setExpectAdSlot($expectAdSlot);
                            $expression->setLibraryExpression($libraryExpression);
                            $expression->setDynamicAdSlot($adSlot);

                            if (!in_array($expression, $existingExpressionsToBeUpdated)) { // for the case add new expression
                                $adSlot->getExpressions()->add($expression);
                            }

                            $this->em->persist($expression);
                        }
                    );
                }

                $this->em->merge($adSlot);
            }

            $this->em->flush();
            $this->em->getConnection()->commit();
        }
        catch(\Exception $ex) {
            $this->em->getConnection()->rollback();

            throw new RuntimeException($ex);
        }
    }
}