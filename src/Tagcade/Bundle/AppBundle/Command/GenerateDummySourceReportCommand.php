<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDummySourceReportCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:dummy-source-report:create')
            ->addOption('publisher', 'p', InputOption::VALUE_REQUIRED, 'Publisher id')
            ->addOption('startDate', 'f', InputOption::VALUE_REQUIRED, 'Start date (YYYY-MM-DD) of the report. ')
            ->addOption('endDate', 't', InputOption::VALUE_OPTIONAL, 'End date of the report (YYYY-MM-DD). Default is yesterday', (new \DateTime('yesterday'))->format('Ymd'))
            ->setDescription('Generate dummy source report for a publisher');
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startDate = $input->getOption('startDate');
        $endDate = $input->getOption('endDate');
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $today = new \DateTime('today');

        if ($startDate > $endDate || $endDate >= $today) {
            throw new \Exception('startDate must be less than or equal to endDate and endDate must not exceed today');
        }

        $siteManager = $this->getContainer()->get('tagcade.domain_manager.site');
        $sites = $siteManager->all();
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval ,$endDate);

        foreach($dateRange as $date) {
            // TODO fetch report for this date and only create if there is no report
            $this->createReportForSitesOnDate($sites, $date);
        }
    }

    protected function createReportForSitesOnDate(array $sites, \DateTime $date)
    {
        /**
         * @var EntityManagerInterface $em
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        foreach($sites as $site) {
            $report = new \Tagcade\Entity\Report\SourceReport\Report();
            $report->setDate($date);
            $report->setSite($site);

            $report->addRecord(
                $this->createRecord([
                        'utm_term' => 'test1',
                        'utm_campaign' => 'test'
                    ])
            );

            $report->addRecord(
                $this->createRecord([
                        'utm_term' => 'test2',
                        'utm_campaign' => 'test'
                    ])
            );

            $em->persist($report);
        }

        $em->flush();
    }

    protected function createRecord(array $trackingKeys) {
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
} 