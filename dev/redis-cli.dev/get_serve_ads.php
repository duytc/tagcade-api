<?php

$queryString = urldecode($_SERVER['QUERY_STRING']);
$query = explode('&', $queryString);

$adSlotId = str_replace('adSlotId=', '', $query[0]);
$queryString = '?' . implode('&', array_slice($query, 1));
$url = 'http://serve.tagcade.dev/ads/tags/slot/' . $adSlotId . $queryString;

echo file_get_contents($url);