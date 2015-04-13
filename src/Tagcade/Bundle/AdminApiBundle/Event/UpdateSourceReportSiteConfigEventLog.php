<?php

namespace Tagcade\Bundle\AdminApiBundle\Event;

class UpdateSourceReportSiteConfigEventLog extends UpdateSourceReportConfigEventLog
{
    /**
     * @inheritdoc
     */
    public function getData()
    {
        $this->setEntityClassName('SourceReportSiteConfig');

        return parent::getData();
    }
}
