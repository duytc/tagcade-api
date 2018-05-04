<?php


namespace Tagcade\DomainManager\Behaviors;


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\LibraryExpression;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\PublicSimpleException;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;

trait RemoveLibraryAdSlotTrait {
    /**
     * validate when deleting an LibraryDisplayAdSlot or LibraryNativeAdSlot
     *
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     */
    protected function removeLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot) {
        $adSlots = $libraryAdSlot->getAdSlots();
        $ronAdSlot = $libraryAdSlot->getRonAdSlot();

        if (($adSlots !== null && count($adSlots) > 0) || $ronAdSlot instanceof RonAdSlotInterface) {
            throw new PublicSimpleException('There are some slots still referring to this library');
        }

        if($libraryAdSlot instanceof LibraryDisplayAdSlotInterface || $libraryAdSlot instanceof LibraryNativeAdSlotInterface) {
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
            if (!empty($libraryExpressions) || !empty($libraryDynamicAdSlots)) {
                throw new LogicException('There are some dynamic libraries still referring to this library');
            }
        }

        $this->getEntityManager()->remove($libraryAdSlot);
        $this->getEntityManager()->flush();
    }

    /**
     * @return EntityManagerInterface
     */
    abstract protected function getEntityManager();
}