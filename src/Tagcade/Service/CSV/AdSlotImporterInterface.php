<?php


namespace Tagcade\Service\CSV;


interface AdSlotImporterInterface
{
    /**
     * @param $filename
     * @param $headerRow
     * @param $outputFileName
     * @param $csvSeparator
     * @return mixed
     */
    public function importCsv($filename, $headerRow, $outputFileName, $csvSeparator);
}