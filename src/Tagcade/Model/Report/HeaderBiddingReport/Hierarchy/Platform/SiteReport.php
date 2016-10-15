<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\HeaderBiddingReport\AbstractCalculatedReport;
use Tagcade\Model\Report\HeaderBiddingReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;

class SiteReport extends AbstractCalculatedReport implements SiteReportInterface
{
    use SuperReportTrait;

    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @inheritdoc
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return int|null
     */
    public function getSiteId()
    {
        if ($this->site instanceof SiteInterface) {
            return $this->site->getId();
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getSiteName()
    {
        if ($this->site instanceof SiteInterface) {
            return $this->site->getName();
        }

        return null;
    }

    /**
     * @param SiteInterface|null $site
     * @return $this
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    protected function setDefaultName()
    {
        $this->setName($this->site->getName());
    }

}