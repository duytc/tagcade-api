<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\LibraryDisplayAdSlot as LibraryDisplayAdSlotModel;

class LibraryDisplayAdSlot extends LibraryDisplayAdSlotModel
{
    protected $id;
    protected $name;
    protected $width;
    protected $height;
    protected $autoFit;
    protected $visible;
    protected $passbackMode;
    protected $libSlotTags;
    protected $ronAdSlot;
    protected $publisher;
    protected $displayAdSlots;
    protected $buyPrice;

    public function __construct()
    {}

    /**
     * @param array $fieldValues
     * @return LibraryDisplayAdSlot
     */
    public static function createLibraryDisplayAdSlotFromArray(array $fieldValues)
    {
        $libraryAdSlotObject = new self();
        foreach ($fieldValues as $field=>$value) {
            $method = sprintf('set%s', ucwords($field));
            $libraryAdSlotObject->$method($value);
        }

        return $libraryAdSlotObject;
    }

}