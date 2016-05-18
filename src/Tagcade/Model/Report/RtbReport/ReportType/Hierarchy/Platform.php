<?php

namespace Tagcade\Model\Report\RtbReport\ReportType\Hierarchy;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\RtbReport\Hierarchy\AccountReportInterface;
use Tagcade\Model\Report\RtbReport\Hierarchy\PlatformReportInterface;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\User\Role\PublisherInterface;

class Platform extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform';

    /** @var PublisherInterface[] */
    protected $publishers;

    public function __construct(array $publishers)
    {
        foreach($publishers as $publisher) {
            /** @var PublisherInterface $publisher */
            if (!$publisher instanceof PublisherInterface) {
                throw new InvalidArgumentException('parameter must be an array of publishers');
            }

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