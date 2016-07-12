<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\LibraryDynamicAdSlot as LibraryDynamicAdSlotModel;

class LibraryDynamicAdSlot extends LibraryDynamicAdSlotModel
{
    protected $id;
    protected $name;
    protected $ronAdSlot;
    protected $publisher;
    protected $dynamicAdSlots;
    protected $libraryExpressions;
    protected $defaultLibraryAdSlot;
    protected $visible;
    protected $native;

    public function __construct()
    {
        $this->libraryExpressions = new ArrayCollection();
    }

    /**
     * @param array $fieldValues
     * @return LibraryDynamicAdSlot
     */
    public static function createLibraryDynamicAdSlotFromArray(array $fieldValues)
    {
        $libraryDynamicAdSlot = new self();

        foreach ($fieldValues as $field=>$value) {
            $method = sprintf('set%s',ucwords($field));
            $libraryDynamicAdSlot->$method($value);
        }
        return $libraryDynamicAdSlot;
    }
}