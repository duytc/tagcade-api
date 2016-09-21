<?php

namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class Account extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.account';

    /**
     * @var PublisherInterface
     */
    private $publisher;
    protected static $supportedMinBreakDown = ['publisher', 'day'];

    public function __construct($publisher = null)
    {
        if ($publisher instanceof PublisherInterface) {
            $this->publisher = $publisher;
        }
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return int|null
     */
    public function getPublisherId()
    {
        return $this->publisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
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
        $firstCondition = !empty($filterParameter->getPublishers()) ||
            $breakDownParameter->hasPublishers();

        $secondCondition =
            empty($filterParameter->getVideoDemandPartners())
            && empty($filterParameter->getVideoWaterfallTags())
            && empty($filterParameter->getVideoDemandAdTags())
            && empty($filterParameter->getVideoPublishers())
            && !$breakDownParameter->hasVideoDemandPartners()
            && !$breakDownParameter->hasVideoWaterfallTags()
            && !$breakDownParameter->hasVideoDemandAdTags()
            && !$breakDownParameter->hasVideoPublishers();

        return $firstCondition && $secondCondition;
    }

    /**
     * @inheritdoc
     */
    public function getVideoObjectId()
    {
        return $this->getPublisherId();
    }
}