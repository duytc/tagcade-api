<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DisplayAdSlot as DisplayAdSlotModel;

class DisplayAdSlot extends DisplayAdSlotModel
{
    protected $id;
    protected $site;
    protected $rtbStatus;
    protected $floorPrice;
    protected $deletedAt;
    protected $hbBidPrice;

    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $fieldValues
     * @return DisplayAdSlot
     */
    public static function createDisplayAdSlotFromArray(array $fieldValues)
    {
        $displayAdSlotObject = new self();
        foreach ($fieldValues as $field=>$value) {
            $method = sprintf('set%s', ucwords($field));
            $displayAdSlotObject->$method($value);
        }

        return $displayAdSlotObject;
    }
}