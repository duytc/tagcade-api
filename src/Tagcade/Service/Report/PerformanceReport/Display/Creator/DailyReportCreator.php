<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;

class DailyReportCreator
{
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var ReportCreatorInterface
     */
    private $reportCreator;

    public function __construct(ObjectManager $om, ReportCreatorInterface $reportCreator)
    {
        $this->om = $om;
        $this->reportCreator = $reportCreator;
    }

    /**
     * Create all reports and persist them
     *
     * @param PublisherInterface[] $publishers
     * @param AdNetworkInterface[] $adNetworks
     */
    public function createAndSave(array $publishers, array $adNetworks)
    {
        $createdReports = [];

        $platformReport = $this->reportCreator->getReport(
            new PlatformReportType($publishers)
        );

        $this->om->persist($platformReport);
        $createdReports[] = $platformReport;

        foreach($adNetworks as $adNetwork) {
            $adNetworkReport = $this->reportCreator->getReport(
                new AdNetworkReportType($adNetwork)
            );

            $this->om->persist($adNetworkReport);
            $createdReports[] = $adNetworkReport;
        }

        $this->om->flush();

        foreach($createdReports as $index => &$tempReport) {
            $this->om->detach($tempReport);
            $createdReports[$index] = null;
        }

        $createdReports = null;
    }

    /**
     * @return DateTime
     */
    public function getReportDate()
    {
        return $this->reportCreator->getDate();
    }

    /**
     * @param DateTime $reportDate
     * @return $this
     */
    public function setReportDate(DateTime $reportDate)
    {
        $this->reportCreator->setDate($reportDate);

        return $this;
    }


}