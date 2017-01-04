<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\PagerParam;

interface LibrarySlotTagRepositoryInterface extends ObjectRepository
{
    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return LibrarySlotTagInterface[]
     */
    public function getByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null);

    public function getByLibraryAdSlotWithPagination(BaseLibraryAdSlotInterface $libraryAdSlot, PagerParam $param);

    public function getLibrarySlotTagIdsByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null);

    /**
     * ReferenceId is unique in each library ad slot so this query always return only one entity or null
     *
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $refId
     * @return LibrarySlotTagInterface|null
     */
    public function getByLibraryAdSlotAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId);

    /**
     * get all librarySlotTags By LibraryAdSlot And Differ RefId (not include the ad tag with refId)
     *
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $refId
     * @return LibrarySlotTagInterface|null
     */
    public function getByLibraryAdSlotAndDifferRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId);

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @return mixed
     */
    public function getByLibraryAdTag(LibraryAdTagInterface $libraryAdTag);
}