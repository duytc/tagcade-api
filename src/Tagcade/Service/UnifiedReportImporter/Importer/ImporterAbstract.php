<?php


namespace Tagcade\Service\UnifiedReportImporter\Importer;


abstract class ImporterAbstract
{
    /** @var int */
    protected $batchSize;
    /**
     * @param int $batchSize
     */
    public function setBatchSize($batchSize)
    {
        if (is_int($batchSize) && $batchSize > 0) {
            $this->batchSize = $batchSize;
        }
    }
    /**
     * @return int $batchSize
     */
    public function getBatchSize()
    {
        return $this->batchSize;
    }
}