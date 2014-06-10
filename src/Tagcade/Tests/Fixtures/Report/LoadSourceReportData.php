<?php

namespace Tagcade\Tests\Fixtures\Report;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DateTime;
use Tagcade\Entity\Report\SourceReport\Report;
use Tagcade\Entity\Report\SourceReport\TrackingKey;
use Tagcade\Entity\Report\SourceReport\Record;
use Tagcade\Entity\Report\SourceReport\TrackingTerm;

class LoadSourceReportData implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $mysite1 = (new Report())
            ->setDate($this->getDate(2014, 6, 1))
            ->setSite('mysite.com')
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

        $mysite2 = (new Report())
            ->setDate($this->getDate(2014, 6, 2))
            ->setSite('mysite.com')
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

        $anotherdomain1 = (new Report())
            ->setDate($this->getDate(2014, 6, 1))
            ->setSite('anotherdomain.com')
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

        $manager->persist($mysite1);
        $manager->persist($mysite2);
        $manager->persist($anotherdomain1);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
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