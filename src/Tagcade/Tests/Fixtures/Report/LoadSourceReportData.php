<?php

namespace Tagcade\Tests\Fixtures\Report;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DateTime;
use Tagcade\Entity\Report\SourceReport\Report;
use Tagcade\Entity\Report\SourceReport\TrackingKey;
use Tagcade\Entity\Report\SourceReport\Record;
use Tagcade\Entity\Report\SourceReport\TrackingTerm;

class LoadSourceReportData implements  FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // need to see about loading references from another entity manager
        $siteId1 = 1;
        $siteId2 = 2;

        $mysitereport1 = (new Report())
            ->setDate($this->getDate(2014, 6, 1))
            ->setSiteId($siteId1)
            ->addRecord(
                (new Record())
                    ->addTrackingKey($this->createTrackingKey('utm_source', '111_111'))
                    ->addTrackingKey($this->createTrackingKey('utm_campaign', 'bl'))
                    ->setDisplayOpportunities(10)
                    ->setDisplayImpressions(5)
                    ->setDisplayClicks(2)
                    ->setVideoPlayerReady(20)
                    ->setVideoAdPlays(20)
                    ->setVideoAdImpressions(15)
                    ->setVideoAdCompletions(10)
                    ->setVideoAdClicks(5)
                    ->setVisits(30)
                    ->setPageViews(40)
                    ->setQtos(25)
            )
            ->addRecord(
                (new Record())
                    ->addTrackingKey($this->createTrackingKey('utm_source', '222_222'))
                    ->addTrackingKey($this->createTrackingKey('utm_campaign', 'bl'))
                    ->setDisplayOpportunities(20)
                    ->setDisplayImpressions(10)
                    ->setDisplayClicks(2)
                    ->setVideoPlayerReady(40)
                    ->setVideoAdPlays(20)
                    ->setVideoAdImpressions(15)
                    ->setVideoAdCompletions(10)
                    ->setVideoAdClicks(5)
                    ->setVisits(30)
                    ->setPageViews(50)
                    ->setQtos(25)
            )
        ;

        $mysitereport2 = (new Report())
            ->setDate($this->getDate(2014, 6, 2))
            ->setSiteId($siteId1)
            ->addRecord(
                (new Record())
                    ->addTrackingKey($this->createTrackingKey('utm_source', '111_111'))
                    ->addTrackingKey($this->createTrackingKey('utm_campaign', 'bl'))
                    ->setDisplayOpportunities(10)
                    ->setDisplayImpressions(5)
                    ->setDisplayClicks(2)
                    ->setVideoPlayerReady(20)
                    ->setVideoAdPlays(20)
                    ->setVideoAdImpressions(15)
                    ->setVideoAdCompletions(10)
                    ->setVideoAdClicks(5)
                    ->setVisits(30)
                    ->setPageViews(40)
                    ->setQtos(25)
            )
        ;

        $anotherdomainreport1 = (new Report())
            ->setDate($this->getDate(2014, 6, 1))
            ->setSiteId($siteId2)
            ->addRecord(
                (new Record())
                    ->addTrackingKey($this->createTrackingKey('utm_source', '111_111'))
                    ->addTrackingKey($this->createTrackingKey('utm_campaign', 'bl'))
                    ->setDisplayOpportunities(10)
                    ->setDisplayImpressions(5)
                    ->setDisplayClicks(2)
                    ->setVideoPlayerReady(20)
                    ->setVideoAdPlays(20)
                    ->setVideoAdImpressions(15)
                    ->setVideoAdCompletions(10)
                    ->setVideoAdClicks(5)
                    ->setVisits(30)
                    ->setPageViews(40)
                    ->setQtos(25)
            )
            ->addRecord(
                (new Record())
                    ->addTrackingKey($this->createTrackingKey('utm_source', '111_111'))
                    ->addTrackingKey($this->createTrackingKey('utm_campaign', 'bl'))
                    ->setDisplayOpportunities(10)
                    ->setDisplayImpressions(5)
                    ->setDisplayClicks(2)
                    ->setVideoPlayerReady(20)
                    ->setVideoAdPlays(20)
                    ->setVideoAdImpressions(15)
                    ->setVideoAdCompletions(10)
                    ->setVideoAdClicks(5)
                    ->setVisits(30)
                    ->setPageViews(40)
                    ->setQtos(25)
            )
        ;

        $manager->persist($mysitereport1);
        $manager->persist($mysitereport2);
        $manager->persist($anotherdomainreport1);

        $manager->flush();
    }

    protected function getDate($y = 2014, $m = 6, $d = 1)
    {
        return (new DateTime())
            ->setDate($y, $m, $d)
            ->setTime(0, 0, 0)
        ;
    }

    protected function createTrackingKey($term, $value)
    {
        return (new TrackingKey())
            ->setTrackingTerm(
                (new TrackingTerm())
                    ->setTerm($term)
            )
            ->setValue($value)
        ;
    }
}