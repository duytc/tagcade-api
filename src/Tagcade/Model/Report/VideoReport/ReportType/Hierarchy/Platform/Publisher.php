<?php

namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PublisherReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractCalculatedReportType;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class Publisher extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.publisher';

    /**
     * @var VideoPublisherInterface
     */
    private $videoPublisher;

    protected static $supportedMinBreakDown = ['videoPublisher', 'day'];

    public function __construct($publisher = null)
    {
        if ($publisher instanceof VideoPublisherInterface) {
            $this->videoPublisher = $publisher;
        }
    }

    /**
     * @return VideoPublisherInterface
     */
    public function getVideoPublisher()
    {
        return $this->videoPublisher;
    }

    /**
     * @return int|null
     */
    public function getVideoPublisherId()
    {
        if ($this->videoPublisher instanceof VideoPublisherInterface) {
            return $this->videoPublisher->getId();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof PublisherReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof WaterfallTagReportInterface;
    }

    /**
     * check if Supports Params and Breakdowns
     *
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     */
    public function isSupportParams(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        $firstCondition = !empty($filterParameter->getVideoPublishers()) ||
            $breakDownParameter->hasVideoPublishers();

        $secondCondition =
            empty($filterParameter->getVideoDemandPartners())
            && empty($filterParameter->getVideoWaterfallTags())
            && empty($filterParameter->getVideoDemandAdTags())
            && !$breakDownParameter->hasVideoDemandPartners()
            && !$breakDownParameter->hasVideoWaterfallTags()
            && !$breakDownParameter->hasVideoDemandAdTags();

        return $firstCondition && $secondCondition;
    }

    /**
     * @inheritdoc
     */
    public function getVideoObjectId()
    {
        return $this->getVideoPublisherId();
    }
}