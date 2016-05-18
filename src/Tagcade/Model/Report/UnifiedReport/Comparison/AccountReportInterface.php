<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReportInterface as PerformanceAccountReportInterface;
use Tagcade\Model\Report\UnifiedReport\Publisher\PublisherReportInterface as UnifiedAccountReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AccountReportInterface extends ComparisonReportInterface
{
    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return PerformanceAccountReportInterface
     */
    public function getPerformanceAccountReport();

    /**
     * @param PerformanceAccountReportInterface $performanceAccountReport
     * @return self
     */
    public function setPerformanceAccountReport($performanceAccountReport);

    /**
     * @return UnifiedAccountReportInterface
     */
    public function getUnifiedAccountReport();

    /**
     * @param UnifiedAccountReportInterface $unifiedAccountReport
     * @return self
     */
    public function setUnifiedAccountReport($unifiedAccountReport);
}