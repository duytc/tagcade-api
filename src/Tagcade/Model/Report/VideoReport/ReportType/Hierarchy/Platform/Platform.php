<?php

namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class Platform extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.platform';

    /**
     * @var null|array|PublisherInterface|PublisherInterface[]
     */
    protected $publishers;
    protected static $supportedMinBreakDown = ['day'];

    public function __construct($publishers = null)
    {
        // convert to array if not null
        if (null != $publishers && !is_array($publishers)) {
            $publishers = [$publishers];
        }

        if (!is_array($publishers)) {
            return;
        }

        // do setting publishers
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                throw new InvalidArgumentException('parameter must be an array of publishers');
            }

            if ($publisher->isTestAccount()) {
                continue;
            }

            /** @var UserEntityInterface $publisher */
            if ($publisher->isEnabled()) {
                $this->publishers[] = $publisher;
            }
        }
    }

    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof PlatformReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
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
        return
            // not filter by any fields
            empty($filterParameter->getPublishers())
            && empty($filterParameter->getVideoPublishers())
            && empty($filterParameter->getVideoDemandPartners())
            && empty($filterParameter->getVideoWaterfallTags())
            && empty($filterParameter->getVideoDemandAdTags())
            // and not breakdown by any fields
            && !$breakDownParameter->hasPublishers()
            && !$breakDownParameter->hasVideoPublishers()
            && !$breakDownParameter->hasVideoDemandPartners()
            && !$breakDownParameter->hasVideoWaterfallTags()
            && !$breakDownParameter->hasVideoDemandAdTags();
    }

    /**
     * @inheritdoc
     */
    public function getVideoObjectId()
    {
        return 'all';
    }
}