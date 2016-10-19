<?php


namespace Tagcade\Service\Report\SourceReport\Result;


use DateTime;
use Tagcade\Model\Report\SourceReport\ReportInterface;

interface ReportResultInterface
{
    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @return ReportInterface[]
     */
    public function getReports();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return DateTime
     */
    public function getDate();
}