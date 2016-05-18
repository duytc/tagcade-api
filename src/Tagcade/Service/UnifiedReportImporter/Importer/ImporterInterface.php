<?php

namespace Tagcade\Service\UnifiedReportImporter\Importer;


interface ImporterInterface
{
    /**
     * @param int $batchSize
     */
    public function setBatchSize($batchSize);

    /**
     * @return int $batchSize
     */
    public function getBatchSize();

    /**
     * import Reports, if $aggregate = true then this report will be merged with the existing report
     * which has the same parameters, site e.g
     * @param array $reports
     * @param $override = false
     *
     * @return bool
     */
    public function importReports(array $reports, $override = false);
}