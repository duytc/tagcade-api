<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

interface SiteReportInterface extends CalculatedReportInterface, SuperReportInterface, SubReportInterface
{}