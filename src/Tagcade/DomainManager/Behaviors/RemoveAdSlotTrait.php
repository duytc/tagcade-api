<?php


namespace Tagcade\DomainManager\Behaviors;


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\LibraryExpression;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Repository\Core\DynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\ExpressionRepositoryInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;

trait RemoveAdSlotTrait {
    /**
     * validate when deleting an DisplayAdSlot or NativeAdSlot
     *
     * @param BaseAdSlotInterface $adSlot
     */
    protected function removeAdSlot(BaseAdSlotInterface $adSlot) {
        if(!$adSlot instanceof DisplayAdSlotInterface && !$adSlot instanceof NativeAdSlotInterface) {
            throw new LogicException('expect an DisplayAdSlotInterface or NativeAdSlotInterface');
        }

        $libraryAdSlot = $adSlot->getLibraryAdSlot();
        if(!$libraryAdSlot instanceof LibraryDisplayAdSlotInterface && !$libraryAdSlot instanceof LibraryNativeAdSlotInterface) {
            throw new LogicException('expect an LibraryDisplayAdSlotInterface or LibraryNativeAdSlotInterface');
        }

        /**
         * @var ExpressionRepositoryInterface $expressionRepository
         */
        $expressionRepository = $this->getEntityManager()->getRepository(Expression::class);
        $expressions = $expressionRepository->findBy(array('expectAdSlot' => $adSlot));
        /**
         * @var DynamicAdSlotRepositoryInterface $dynamicRepository
         */
        $dynamicRepository = $this->getEntityManager()->getRepository(DynamicAdSlot::class);
        $defaultSlots = $dynamicRepository->findBy(array('defaultAdSlot' => $adSlot));
        if (!empty($expressions) || !empty($defaultSlots)) { // this ensures that there is existing dynamic slot that one of its expressions containing this slot
            throw new LogicException('There are some dynamic ad slot still referring to this ad slot');
        }

        if (!$libraryAdSlot->isVisible()) { // not shared ad slot
            $this->getEntityManager()->remove($libraryAdSlot); // resulting cascade remove this ad slot
            $this->getEntityManager()->flush();
            return;
        }

        // if the ad slot is in library then we have to if its library is also being referenced
        /**
         * @var LibraryExpressionRepositoryInterface $libraryExpressionRepository
         */
        $libraryExpressionRepository = $this->getEntityManager()->getRepository(LibraryExpression::class);
        $libraryExpressions = $libraryExpressionRepository->getByExpectLibraryAdSlot($libraryAdSlot);
        /**
         * @var LibraryDynamicAdSlotRepositoryInterface $libraryDynamicAdSlotRepository
         */
        $libraryDynamicAdSlotRepository = $this->getEntityManager()->getRepository(LibraryDynamicAdSlot::class);
        $libraryDynamicAdSlots = $libraryDynamicAdSlotRepository->getByDefaultLibraryAdSlot($libraryAdSlot);
        if (!empty($libraryExpressions) ||
            !empty($libraryDynamicAdSlots) ||
            (count($adSlot->getCoReferencedAdSlots()) > 1) ||
            $libraryAdSlot->getRonAdSlot() instanceof RonAdSlotInterface) {
            // only remove the ad slot itself if library dynamic is referencing or more than one ad slot referencing the library, not removing the library
            $this->getEntityManager()->remove($adSlot);
            $this->getEntityManager()->flush();
            return;
        }

        $this->getEntityManager()->remove($libraryAdSlot); // resulting cascade remove this ad slot
        $this->getEntityManager()->flush();
    }

    /**
     * @return EntityManagerInterface
     */
    abstract protected function getEntityManager();
}