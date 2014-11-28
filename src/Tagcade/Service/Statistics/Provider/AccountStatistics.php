<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Statistics\Provider\Fields\TopListFilter;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;

class AccountStatistics implements AccountStatisticsInterface
{
    use TopListFilter;

    /**
     * @var ReportBuilderInterface
     */
    protected $reportBuilder;

    public function __construct(ReportBuilderInterface $reportBuilder)
    {
        $this->reportBuilder = $reportBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getTopPublishersByBilledAmount(Params $params, $limit = 7, $order = 'DESC')
    {
        $params->setGrouped(true);
        $allPublishersReports = $this->reportBuilder->getAllPublishersReport($params);

        return $this->topList($allPublishersReports, $sortBy = 'billedAmount', $limit, $order);
    }

    /**
     * @inheritdoc
     */
    public function getTopAdNetworksByEstRevenueForPublisher(PublisherInterface $publisher, Params $params, $limit = 7, $order = 'DESC')
    {
        $params->setGrouped(true);
        $adNetworksReports = $this->reportBuilder->getPublisherAdNetworksReport($publisher, $params);

        return $this->topList($adNetworksReports, $sortBy = 'estRevenue', $limit, $order);
    }
}