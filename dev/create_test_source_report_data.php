<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');
$siteManager = $container->get('tagcade.domain_manager.site');

$sites = $siteManager->all();
$START_DATE = new DateTime('2015-01-02');
$END_DATE = new DateTime('2015-01-05');

$today = new DateTime('today');
if ($END_DATE >= $today) {
    $END_DATE = new DateTime('yesterday');
}
$END_DATE = $END_DATE->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($START_DATE, $interval, $END_DATE);

foreach ($dateRange as $date) {
    foreach($sites as $site) {
        $report = new Tagcade\Entity\Report\SourceReport\Report;
        $report->setDate($date);
        $report->setSite($site);

        $report->addRecord(
            createRecord([
                'utm_term' => 'test1',
                'utm_campaign' => 'test'
            ])
        );

        $report->addRecord(
            createRecord([
                'utm_term' => 'test2',
                'utm_campaign' => 'test'
            ])
        );

        $em->persist($report);
    }

    $em->flush();
}



function createRecord(array $trackingKeys) {
    $record = new \Tagcade\Entity\Report\SourceReport\Record();

    foreach($trackingKeys as $term => $value) {
        $record->addTrackingKey(
            (new \Tagcade\Entity\Report\SourceReport\TrackingKey())
                ->setTrackingTerm(
                    (new \Tagcade\Entity\Report\SourceReport\TrackingTerm())
                        ->setTerm($term)
                )
                ->setValue($value)
        );
    }

    unset($term, $value);

    $displayOpportunities = mt_rand(1, 10000000);
    $videoPlayerReady = mt_rand(1, 1000000);
    $videoAdPlays = mt_rand($videoPlayerReady/2, $videoPlayerReady);
    $videoStarts = mt_rand($videoAdPlays*5, $videoAdPlays*10);
    $visits = mt_rand(1000, 10000000);
    $pageViews = mt_rand($visits*2, $visits*4);

    $record
        ->setDisplayOpportunities($displayOpportunities)
        ->setDisplayImpressions(mt_rand($displayOpportunities/2, $displayOpportunities))
        ->setDisplayClicks(mt_rand($displayOpportunities*0.001, $displayOpportunities*0.02))
        ->setVideoPlayerReady($videoPlayerReady)
        ->setVideoAdPlays($videoAdPlays)
        ->setVideoAdImpressions(mt_rand($videoAdPlays/2, $videoAdPlays))
        ->setVideoAdCompletions(mt_rand($videoAdPlays*0.4, $videoAdPlays*0.8))
        ->setVideoAdClicks(mt_rand($videoAdPlays*0.001, $videoAdPlays*0.05))
        ->setVideoStarts($videoStarts)
        ->setVideoEnds(mt_rand($videoStarts*0.4, $videoStarts*0.9))
        ->setVisits($visits)
        ->setPageViews($pageViews)
        ->setQtos(mt_rand($pageViews*0.75, $pageViews))
    ;

    return $record;
}