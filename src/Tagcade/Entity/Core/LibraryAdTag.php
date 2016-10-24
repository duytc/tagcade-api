<?php

namespace Tagcade\Entity\Core;
use Tagcade\Model\Core\LibraryAdTag as LibraryAdTagModel;

class LibraryAdTag extends LibraryAdTagModel {

    protected $id;
    protected $html;
    protected $visible = false;
    protected $adNetwork;
    protected $adTags;
    protected $libSlotTags;
    protected $name;
    protected $adType;
    protected $partnerTagId;
    protected $descriptor;
    protected $inBannerDescriptor;

    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;

    function __construct()
    {
    }

    /**
     * @param array $fieldValues
     * @return LibraryAdTag
     */
    public static function createAdTagLibraryFromArray(array $fieldValues)
    {
        $adTagLibraryObject = new self();
        foreach($fieldValues as $field=>$value) {
            $method = sprintf('set%s', ucwords($field));
            $adTagLibraryObject->$method($value);
        }
        return $adTagLibraryObject;
    }
}