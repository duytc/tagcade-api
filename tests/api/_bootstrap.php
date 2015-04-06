<?php
// Here you can initialize variables that will be available to your tests
$endDate = $endMonth = new DateTime;
$startDate = $startMonth = clone $endDate;

$startDate->modify( '-30 day' );
$startMonth->modify( '-3 month' );

define('URL_API', '/v1');
define('URL_ADMIN_API', '/admin/v1');
define('URL_PUBLISHER_API', '/publisher/v1');
define('URL_PERFORMANCE_REPORT', '/reports/v1/performancereports');
define('URL_BILLING_REPORT', '/reports/v1/billing');
define('URL_SOURCE_REPORT', '/reports/v1/sourcereports');
define('URL_STATISTICS', '/statistics/v1');

define('START_DATE', $startDate->format('Y-m-d'));
define('END_DATE', $endDate->format('Y-m-d'));

define('START_MONTH', $startMonth->format('Y-m'));
define('END_MONTH', $endMonth->format('Y-m'));

define('PARAMS_PUBLISHER', 2);
define('PARAMS_SITE', 22);
define('PARAMS_AD_NETWORK', 1);
define('PARAMS_AD_SLOT', 6);
define('PARAMS_AD_TAG', 7);