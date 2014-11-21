<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\User\Role\PublisherInterface;

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

            //if ($publisher->getUser()->hasDisplayModule()) {
                $this->publishers[] = $publisher;
            //}
        }
    }

    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @inheritdoc
     */
    public function isValidReport(ReportInterface $report)
    {
        return $report instanceof PlatformReportInterface;
    }
}