<?php

namespace Tagcade\Service\TagLibrary;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\LibraryAdSlotManagerInterface;
use Tagcade\DomainManager\LibraryAdTagManagerInterface;
use Tagcade\DomainManager\LibraryExpressionManagerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;

class UnlinkService implements UnlinkServiceInterface
{
    /** @var AdTagManagerInterface */
    private $adTagManager;

    /** @var LibraryAdTagManagerInterface */
    private $libraryAdTagManager;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /** @var LibraryAdSlotManagerInterface */
    private $libraryAdSlotManager;

    /** @var EntityManagerInterface */
    private $em;

    /** @var LibraryExpressionManagerInterface */
    private $libraryExpressionManager;

    /**
     * @param AdTagManagerInterface $adTagManager
     * @param LibraryAdTagManagerInterface $libraryAdTagManager
     * @param AdSlotManagerInterface $adSlotManager
     * @param LibraryAdSlotManagerInterface $libraryAdSlotManager
     * @param EntityManagerInterface $em
     * @param LibraryExpressionManagerInterface $libraryExpressionManager
     */
    function __construct(AdTagManagerInterface $adTagManager, LibraryAdTagManagerInterface $libraryAdTagManager,
                         AdSlotManagerInterface $adSlotManager, LibraryAdSlotManagerInterface $libraryAdSlotManager,
                         EntityManagerInterface $em, LibraryExpressionManagerInterface $libraryExpressionManager
    )
    {
        $this->adTagManager = $adTagManager;
        $this->libraryAdTagManager = $libraryAdTagManager;
        $this->adSlotManager = $adSlotManager;
        $this->libraryAdSlotManager = $libraryAdSlotManager;
        $this->em = $em;
        $this->libraryExpressionManager = $libraryExpressionManager;
    }

    /**
     * unlink For Ad Tag
     *
     * @param AdTagInterface $adTag
     * @throws \Exception when conditions failed
     * @return bool
     */
    public function unlinkForAdTag(AdTagInterface $adTag)
    {
        /** @var LibraryAdTagInterface $libAdTag */
        $libAdTag = $adTag->getLibraryAdTag();

        // check if already is not visible (not using shared lib ad tag)
        if (!$libAdTag->getVisible()) {
            return $adTag;
        }

        // check if own ad slot uses library ad slot
        if ($adTag->getAdSlot()->getLibraryAdSlot()->isVisible()) {
            throw new \Exception('Expect own ad slot does not use a standalone ad slot');
        }

        // clone libAdTag to a new not-shared libAdTag and save
        $newLibraryAdTag = clone $libAdTag;
        $newLibraryAdTag
            ->setId(null)
            ->setLibSlotTags(null)
            ->setVisible(false);

        $this->em->persist($newLibraryAdTag);
        $this->em->flush();

        // change relationship of ad tag to the new libAdTag and save
        $adTag
            ->setLibraryAdTag($newLibraryAdTag)
            ->setRefId(uniqid('', true)); // important: reset refId

        $this->em->persist($adTag);
        $this->em->flush();

        return $adTag;
    }

    /**
     * unlink For Ad Slot
     *
     * @param BaseAdSlotInterface $adSlot
     * @return mixed
     */
    public function unlinkForAdSlot(BaseAdSlotInterface $adSlot)
    {
        if ($adSlot instanceof ReportableAdSlotInterface) {
            return $this->unlinkForReportableAdSlot($adSlot);
        }

        if ($adSlot instanceof DynamicAdSlotInterface) {
            return $this->unlinkForDynamicAdSlot($adSlot);
        }

        return false;
    }

    /**
     * @param ReportableAdSlotInterface $reportableAdSlot
     * @return mixed
     */
    private function unlinkForReportableAdSlot(ReportableAdSlotInterface $reportableAdSlot)
    {
        /** @var BaseLibraryAdSlotInterface $libAdSlot */
        $libAdSlot = $reportableAdSlot->getLibraryAdSlot();

        // check if already is not visible (not using shared lib ad tag)
        if (!$libAdSlot->isVisible()) {
            return $reportableAdSlot;
        }

        // unlink this ad slot
        //// clone libAdSlot to a new not-shared libAdSlot and save
        $newLibraryAdSlot = clone $libAdSlot;
        $newLibraryAdSlot
            ->setVisible(false)
            ->setId(null)
            ->setLibSlotTags(null)
            ->setRonAdSlot(null);

        $this->libraryAdSlotManager->save($newLibraryAdSlot);

        //// change relationship of ad slot to the new libAdSlot BUT NOT save, 'save' will be called at the end
        $reportableAdSlot->setLibraryAdSlot($newLibraryAdSlot);

        // unlink all ad tags of this ad slot
        // important: do this step after unlink ad slot because we need make sure ad tag belong to an ad slot that does not use a standalone ad slot
        /** @var AdTagInterface[] | Collection $adTags */
        $adTags = $reportableAdSlot->getAdTags();

        if ($adTags instanceof Collection) {
            $adTags = $adTags->toArray();
        }

        foreach ($adTags as &$adTag) {
            $adTag = $this->unlinkForAdTag($adTag); // use reference '&' for self updating all ad tags after unlink
        }

        // finally, save ad slot
        $this->adSlotManager->save($reportableAdSlot);

        return $reportableAdSlot;
    }

    /**
     * unlink For a Dynamic Ad Slot
     *
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @return mixed
     */
    private function unlinkForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot)
    {
        /** @var LibraryDynamicAdSlotInterface $libDynamicAdSlot */
        $libDynamicAdSlot = $dynamicAdSlot->getLibraryAdSlot();

        // check if already is not visible (not using shared lib ad tag)
        if (!$libDynamicAdSlot->isVisible()) {
            return $dynamicAdSlot;
        }

        // Step.1 unlink this dynamic ad slot
        //// clone libAdSlot to a new not-shared libAdSlot and save
        $newLibDynamicAdSlot = clone $libDynamicAdSlot;
        $newLibDynamicAdSlot
            ->setVisible(false)
            ->setRonAdSlot(null)
            ->setId(null);

        $this->em->persist($newLibDynamicAdSlot);
        $this->em->flush();

        //// change relationship of ad slot to the new libAdSlot and save
        $dynamicAdSlot
            ->setLibraryAdSlot($newLibDynamicAdSlot);

        $this->em->persist($dynamicAdSlot);
        $this->em->flush();

        // Step.2 unlink default ad slot if has - temporarily not need

        // Step.3 unlink all expressions if has
        /** @var ExpressionInterface[]|Collection $expressions */
        $expressions = $dynamicAdSlot->getExpressions();

        if ($expressions instanceof Collection) {
            $expressions = $expressions->toArray();
        }

        //// unlink expression
        foreach ($expressions as $expression) {
            $this->unlinkForExpression($expression);
        }

        //// set new libDynamicAdSlot for newLibExpression
        $newLibDynamicAdSlot->setLibraryExpressions([]);
        foreach ($expressions as $expression) {
            $expression->getLibraryExpression()->setLibraryDynamicAdSlot($newLibDynamicAdSlot);
        }

        //// also, set new libExpressions for newLibDynamicAdSlot (bi-direction)
        $newLibExpressions = array_map(function ($expression) {
            /** @var ExpressionInterface $expression */
            return $expression->getLibraryExpression();
        }, $expressions);

        $newLibDynamicAdSlot->setLibraryExpressions($newLibExpressions);

        // finally, save this dynamicAdSlot
        $this->em->persist($newLibDynamicAdSlot);
        $this->em->persist($dynamicAdSlot);
        $this->em->flush();

        return $dynamicAdSlot;
    }

    /**
     * unlink For an Expression
     *
     * temporarily not used because not need
     *
     * @param ExpressionInterface $expression
     * @return ExpressionInterface
     * @throws \Exception
     */
    private function unlinkForExpression(ExpressionInterface $expression)
    {
        /** @var LibraryExpressionInterface $libExpression */
        $libExpression = $expression->getLibraryExpression();

        // Step.1 clone libExpression to a new not-shared libExpression BUT NOT save, this should be persisted at the end
        $newLibraryExpression = clone $libExpression;
        $newLibraryExpression
            ->setId(null);

        $this->em->persist($newLibraryExpression);
        $this->em->flush();

        // change relationship of expression to the new libExpression BUT NOT save, this should be persisted ad the end
        $expression
            ->setLibraryExpression($newLibraryExpression);

        $this->em->persist($expression);
        $this->em->flush();

        // also, set expressions for newLibraryExpression (bi-direction)
        $newLibraryExpression->setExpressions(array($expression));
        $this->em->persist($newLibraryExpression);
        $this->em->flush();

        // Step.2 unlink for expected ad slot - temporarily not need

        return $expression;
    }
}