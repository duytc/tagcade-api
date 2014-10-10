<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface as AdNetworkModelInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetworkReport;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\Behaviours\HasSubReports;

class AdNetwork extends ReportTypeAbstract implements AdNetworkInterface
{
    use HasSubReports;

    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

    /**
     * @var AdNetworkInterface
     */
    protected $subReportCreator;

    public function __construct(AdTagManagerInterface $adTagManager, AdTagInterface $subReportCreator)
    {
        $this->adTagManager = $adTagManager;
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdNetworkModelInterface $adNetwork)
    {
        $this->syncEventCounterForSubReports();

        $report = new AdNetworkReport();

        $report
            ->setAdNetwork($adNetwork)
            ->setDate($this->getDate())
            ->setName($adNetwork->getName())
        ;

        $adTags = $this->adTagManager->getAdTagsForAdNetwork($adNetwork);

        foreach ($adTags as $adTag) {
            $report->addSubReport(
                $this->subReportCreator->createReport($adTag, $this->getDate())
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function checkParameter($adNetwork)
    {
        return $adNetwork instanceof AdNetworkModelInterface;
    }
}