<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\PublisherReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PublisherReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameter;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoPublisherWaterfallTagTransformer extends AbstractTransformer implements TransformerInterface
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
        if (!$report instanceof WaterfallTagReportInterface) {
            return false;
        }

        return $report->getVideoWaterfallTag()->getVideoPublisher();
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        return ($reportType instanceof WaterfallTag && $breakDownParameter->getMinBreakdown() == BreakDownParameter::VIDEO_PUBLISHER_KEY);
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