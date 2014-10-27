<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

interface PlatformReportInterface extends RootReportInterface, CalculatedReportInterface, SuperReportInterface
{}
