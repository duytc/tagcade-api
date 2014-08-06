<?php

namespace Tagcade\Model\Report\Behaviors;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Exception\InvalidArgumentException;

trait HasSubReports
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $subReports;

    /**
     * @return ArrayCollection
     */
    public function getSubReports()
    {
        return $this->subReports;
    }

    /**
     * @inheritdoc
     */
    public function addSubReport(ReportInterface $report)
    {
        if (!$this->isValidSubReport($report)) {
            throw new InvalidArgumentException('That sub report is valid for this report');
        }

        $report->setDate($this->getDate());
        $this->subReports->add($report);

        return $this;
    }

    abstract public function isValidSubReport(ReportInterface $report);

    /**
     * @return \DateTime
     */
    abstract public function getDate();
}