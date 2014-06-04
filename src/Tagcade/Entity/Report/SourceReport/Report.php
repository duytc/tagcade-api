<?php

namespace Tagcade\Entity\Report\SourceReport;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\SourceReport\Report as ReportModel;

class Report extends ReportModel
{
    public function __construct()
    {
        $this->records = new ArrayCollection();
    }
}