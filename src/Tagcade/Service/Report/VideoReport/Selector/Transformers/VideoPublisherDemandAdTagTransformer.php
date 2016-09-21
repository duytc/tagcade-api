<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\PublisherReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PublisherReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameter;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoPublisherDemandAdTagTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    protected function getTargetClass()
    {
        return PublisherReport::class;
    }

    /**
     * @inheritdoc
     */
    protected function getReportTypeClass()
    {
        return Publisher::class;
    }

    /**
     * @inheritdoc
     */
    protected function getParentObject(ReportInterface $report)
    {
        if (!$report instanceof DemandAdTagReportInterface) {
            return false;
        }

        return $report->getVideoDemandAdTag()->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getVideoPublisher();
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        return ($reportType instanceof DemandAdTag && $breakDownParameter->getMinBreakdown() == BreakDownParameter::VIDEO_PUBLISHER_KEY);
    }

    /**
     * @inheritdoc
     */
    protected function aggregateChildReport(ReportInterface $parentReport, ReportInterface $childReport)
    {
        parent::aggregateChildReport($parentReport, $childReport);

        /** @var PublisherReportInterface $parentReport */
        $parentReport->setVideoPublisher($this->getParentObject($childReport));

        return $parentReport;
    }
}