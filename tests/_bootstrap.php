<?php
// This is global bootstrap for autoloading

\Codeception\Util\Autoload::registerSuffix('Group', __DIR__.DIRECTORY_SEPARATOR.'_groups');

require_once 'api/abstract/AdminApiBundle/ActionLog.php';
require_once 'api/abstract/AdminApiBundle/User.php';
require_once 'api/abstract/ApiBundle/AdNetwork.php';
require_once 'api/abstract/ApiBundle/AdSlot.php';
require_once 'api/abstract/ApiBundle/NativeAdSlot.php';
require_once 'api/abstract/ApiBundle/DynamicAdSlot.php';
require_once 'api/abstract/ApiBundle/AdTag.php';
require_once 'api/abstract/ApiBundle/Site.php';
require_once 'api/abstract/ApiBundle/Token.php';
require_once 'api/abstract/ReportApiBundle/BillingReport.php';
require_once 'api/abstract/ReportApiBundle/PerformanceReport.php';
require_once 'api/abstract/ReportApiBundle/SourceReports.php';
require_once 'api/abstract/StatisticsApiBundle/Statistics.php';
require_once 'api/abstract/PublisherBundle/Publisher.php';