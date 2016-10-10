<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\Fields;

use Tagcade\Exception\InvalidArgumentException;

use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\HeaderBiddingReport\SubReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\SuperReportInterface;

trait SubReportsTrait
{
    /** @var ArrayCollection */
    protected $subReports;

    /**
     * @return ReportInterface[]
     */
    public function getSubReports()
    {
        return $this->subReports->toArray();
    }

    public function addSubReport(ReportInterface $report)
    {
        if (!$this->isValidSubReport($report)) {
            throw new InvalidArgumentException('That sub report is not valid for this report');
        }

        $report->setDate($this->getDate());

        if ($report instanceof SubReportInterface) {
            $report->setSuperReport($this);
        }

        $this->subReports->add($report);

        return $this;
    }

    public function isGrandParents()
    {
        return $this->subReports->first() instanceof SuperReportInterface;
    }

    abstract public function isValidSubReport(ReportInterface $report);

    /**
     * @return \DateTime
     */
    abstract public function getDate();
}