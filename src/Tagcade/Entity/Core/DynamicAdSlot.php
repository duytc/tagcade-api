<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DynamicAdSlot as DynamicAdSlotModel;

class DynamicAdSlot extends DynamicAdSlotModel
{
    protected $id;
    protected $site;
    protected $expressions;
    protected $defaultAdSlot;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $fieldValues
     * @return DynamicAdSlot
     */
    public static function createDynamicAdSlotFromArray($fieldValues)
    {
        $dynamicAdSlot = new self();
        foreach ($fieldValues as $field=>$value) {
            $method = sprintf('set%s', ucwords($field));
            $dynamicAdSlot->$method($value);
        }

        return $dynamicAdSlot;
    }
}