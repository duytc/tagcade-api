<?php

namespace Tagcade\Handler\Report;

use Doctrine\Common\Persistence\ObjectManager;
use \DateTime;

class SourceReportHandler
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function getReports($domain, $dateTo, $dateFrom)
    {
        return [];
    }
}