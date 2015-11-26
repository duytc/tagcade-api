<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors;


interface SelectorInterface
{
    public function getReports();

    public function supportsReportType();
}