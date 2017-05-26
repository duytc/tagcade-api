<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\AdTag as AdTagModel;

class AdTag extends AdTagModel
{
    protected $id;
    protected $adSlot;
    protected $position;
    protected $active;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;
    protected $frequencyCap;
    protected $rotation;
    protected $refId;
    protected $libraryAdTag;
    protected $impressionCap;
    protected $networkOpportunityCap;
    protected $passback;

    public function __construct()
    {
    }

    /**
     * @param array $fieldValues
     * @return AdTag
     */
    public static function createAdTagFromArray(array $fieldValues)
    {
        $adTagObject = new self();
        foreach ($fieldValues as $field => $value) {
            $method = sprintf('set%s', ucwords($field));
            $adTagObject->$method($value);
        }
        return $adTagObject;
    }

}