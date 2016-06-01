<?php


namespace Tagcade\Model\Report\RtbReport\Hierarchy;


use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\RtbReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\RtbReport\ReportInterface;

class SiteReport extends AbstractCalculatedReport implements SiteReportInterface
{
    use SuperReportTrait;

    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @return SiteInterface
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
        if ($this->site instanceof SiteInterface) {
            $this->setName($this->site->getName());
        }
    }
}