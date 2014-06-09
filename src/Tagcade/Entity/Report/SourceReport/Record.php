<?php

namespace Tagcade\Entity\Report\SourceReport;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\SourceReport\Record as RecordModel;

class Record extends RecordModel
{
    /**
     * @var string
     */
    protected $trackingKeysInline;

    public function __construct()
    {
        $this->trackingKeys = new ArrayCollection();
    }
}