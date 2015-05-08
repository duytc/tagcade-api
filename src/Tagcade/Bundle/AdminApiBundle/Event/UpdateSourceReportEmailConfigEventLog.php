<?php

namespace Tagcade\Bundle\AdminApiBundle\Event;

class UpdateSourceReportEmailConfigEventLog extends UpdateSourceReportConfigEventLog
{
    /**
     * @inheritdoc
     */
    public function getData()
    {
        $this->setEntityClassName('SourceReportEmailConfig');

        return parent::getData();
    }
}
