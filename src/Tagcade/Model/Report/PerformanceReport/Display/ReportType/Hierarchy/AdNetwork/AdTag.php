<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class AdTag implements ReportTypeInterface
{
    /**
     * @var AdTagInterface
     */
    private $adTag;

    public function __construct(AdTagInterface $adTag)
    {
        $this->adTag = $adTag;
    }

    /**
     * @return AdTagInterface
     */
    public function getAdTag()
    {
        return $this->adTag;
    }
}