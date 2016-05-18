<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class Platform extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.platform';

    /**
     * @var PublisherInterface[]
     */
    protected $publishers;

    public function __construct(array $publishers)
    {
        foreach($publishers as $publisher) {
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
}