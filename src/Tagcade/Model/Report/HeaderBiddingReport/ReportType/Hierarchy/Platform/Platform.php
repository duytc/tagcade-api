<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\AbstractReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class Platform extends AbstractReportType implements CalculatedReportTypeInterface
{
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