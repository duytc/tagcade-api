<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\RootReportInterface;
use Tagcade\Model\Report\VideoReport\SuperReportInterface;

interface PlatformReportInterface extends ReportInterface, SuperReportInterface, RootReportInterface
{}