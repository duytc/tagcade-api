<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\Expression as ExpressionModel;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;

class Expression extends ExpressionModel
{
    protected $id;

    protected $expressionDescriptor;
    protected $startingPosition;
    protected $expressionInJs;
    /**
     * @var LibraryDynamicAdSlotInterface
     */
    protected $libraryDynamicAdSlot;
    /**
     * @var BaseAdSlotInterface $expectAdSlot
     */
    protected $expectAdSlot;

    public function __construct()
    {}

    /**
     * @return LibraryDynamicAdSlotInterface
     */
    public function getLibraryDynamicAdSlot()
    {
        return $this->libraryDynamicAdSlot;
    }

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     */
    public function setLibraryDynamicAdSlot($libraryDynamicAdSlot)
    {
        $this->libraryDynamicAdSlot = $libraryDynamicAdSlot;
    }


    public function getDynamicAdSlot(){

    }

    /**
     * @return BaseAdSlotInterface
     */
    public function getExpectAdSlot()
    {
        return $this->expectAdSlot;
    }

    /**
     * @param ReportableAdSlotInterface $expectAdSlot
     */
    public function setExpectAdSlot(ReportableAdSlotInterface $expectAdSlot)
    {
        $this->expectAdSlot = $expectAdSlot;
    }
}