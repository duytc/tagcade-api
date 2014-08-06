<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\Behaviors\CalculatedDisplayReport;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\Behaviors\HasSubReports;
use Tagcade\Model\Report\Behaviors\HasSuperReport;

/**
 * This is for reports that have a 'super report'
 *
 * i.e An AdSlotReport has a SiteReport as its super report
 * A platform report is the top tier and does not have a super report
 */
abstract class AbstractCalculatedReportWithSuper extends AbstractCalculatedReport
{
    use HasSuperReport;
}