<?php

namespace Tagcade\Service\Report\VideoReport\Creator;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Counter\VideoEventCounterInterface;

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
     * @param boolean $dataWithDateHour
     * @return self
     */
    public function setDataWithDateHour($dataWithDateHour);

    /**
     * @return boolean
     */
    public function getDataWithDateHour();

    /**
     * @return VideoEventCounterInterface
     */
    public function getEventCounter();

    /**
     * @param ReportTypeInterface $reportType
     * @return ReportTypeInterface
     * @throws InvalidArgumentException usually if the parameter is incorrect for the supplied report type or the
     *                                  report type does not exist
     */
    public function getReport(ReportTypeInterface $reportType);
}