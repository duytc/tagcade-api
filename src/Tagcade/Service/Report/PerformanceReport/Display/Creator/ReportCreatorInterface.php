<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\ReportTypeInterface;
use Tagcade\Exception\InvalidArgumentException;

interface ReportCreatorInterface
{
    /**
     * @param DateTime $date
     * @return self
     */
    public function setDate(DateTime $date);

    /**
     * @return DateTime|null
     */
    public function getDate();

    /**
     * @param string $name
     * @param ReportTypeInterface $reportType
     */
    public function addReportType($name, ReportTypeInterface $reportType);

    /**
     * @param string $name
     * @param mixed $parameter The parameter is passed to createReport() method of ReportTypeInterface children
     * @return ReportTypeInterface
     * @throws InvalidArgumentException usually if the parameter is incorrect for the supplied report type or the
     *                                  report type does not exist
     */
    public function getReport($name, $parameter);
}