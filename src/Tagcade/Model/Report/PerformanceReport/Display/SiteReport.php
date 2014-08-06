<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Tagcade\Model\Core\SiteInterface;

class SiteReport extends AbstractCalculatedReport implements SiteReportInterface
{
    protected $site;

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param SiteInterface|null $site
     * @return self
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

    protected function setDefaultName()
    {
        if ($site = $this->getSite()) {
            $this->setName($site->getName());
        }
    }
}