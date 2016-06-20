<?php


namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\LibraryAdTagInterface;

interface AdTagGeneratorInterface
{
    /**
     * @param LibraryAdTagInterface $adTagLibrary
     * @param array $adSlots
     * @return mixed
     */
    public function generateAdTagFromMultiAdSlot(LibraryAdTagInterface $adTagLibrary, array $adSlots);
}