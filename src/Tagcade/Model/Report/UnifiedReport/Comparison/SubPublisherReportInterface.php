<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportInterface as PerformanceSubPublisherReportInterface;
use Tagcade\Model\Report\UnifiedReport\Publisher\SubPublisherReportInterface as UnifiedSubPublisherReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherReportInterface extends ComparisonReportInterface
{
    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher);

    /**
     * @return PerformanceSubPublisherReportInterface
     */
    public function getPerformanceSubPublisherReport();

    /**
     * @param PerformanceSubPublisherReportInterface $performanceSubPublisherReport
     * @return self
     */
    public function setPerformanceSubPublisherReport($performanceSubPublisherReport);

    /**
     * @return UnifiedSubPublisherReportInterface
     */
    public function getUnifiedSubPublisherReport();

    /**
     * @param UnifiedSubPublisherReportInterface $unifiedSubPublisherReport
     * @return self
     */
    public function setUnifiedSubPublisherReport($unifiedSubPublisherReport);
}