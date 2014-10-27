<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

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
        $platformReport = $this->reportCreator->getReport(
            new PlatformReportType($publishers)
        );

        $this->om->persist($platformReport);

        foreach($adNetworks as $adNetwork) {
            $adNetworkReport = $this->reportCreator->getReport(
                new AdNetworkReportType($adNetwork)
            );

            $this->om->persist($adNetworkReport);
        }

        $this->om->flush();
    }
}