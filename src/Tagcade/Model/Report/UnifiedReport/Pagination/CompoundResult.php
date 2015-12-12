<?php


namespace Tagcade\Model\Report\UnifiedReport\Pagination;


class CompoundResult
{
    /**
     * @var array
     */
    protected $items;
    /**
     * @var int
     */
    protected $count;

    function __construct($items, $count)
    {
        $this->items = $items;
        $this->count = $count;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}