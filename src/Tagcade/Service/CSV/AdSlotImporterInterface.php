<?php


namespace Tagcade\Service\CSV;


interface AdSlotImporterInterface
{
    /**
     * @param $filename
     * @param array $headers
     * @param $headerRow
     * @return bool
     */
    public function checkCSVFileFormat($filename, array $headers, $headerRow);

    /**
     * @param $filename
     * @return mixed
     */
    public function import($filename);
}