<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Statistics\Provider\Behaviors\TopListFilterTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;

class AccountStatistics implements AccountStatisticsInterface
{
    use TopListFilterTrait;

    /**
     * @var ReportBuilderInterface
     */
    protected $reportBuilder;
    /**
     * @var ProjectedBillingCalculatorInterface
     */
    protected $projectedBillingCalculator;

    public function __construct(ReportBuilderInterface $reportBuilder, ProjectedBillingCalculatorInterface $projectedBillingCalculator)
    {
        $this->reportBuilder = $reportBuilder;
        $this->projectedBillingCalculator = $projectedBillingCalculator;
    }

    public function getTopPublishersByBilledAmount(Params $params, $limit = 10)
    {
        $params->setGrouped(true);
        $allPublishersReports = $this->reportBuilder->getAllPublishersReport($params);

        return $this->topList($allPublishersReports, $sortBy = 'billedAmount', $limit);
    }

    public function getTopAdNetworksByEstRevenueForPublisher(PublisherInterface $publisher, Params $params, $limit = 10)
    {
        $params->setGrouped(true);
        $adNetworksReports = $this->reportBuilder->getPublisherAdNetworksReport($publisher, $params);

        return $this->topList($adNetworksReports, $sortBy = 'estRevenue', $limit);
    }

    public function getProjectedBilledAmount(PublisherInterface $publisher)
    {
        return $this->projectedBillingCalculator->calculateProjectedBilledAmountForPublisher($publisher);
    }


}