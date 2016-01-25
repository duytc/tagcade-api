<?php


namespace Tagcade\Service\CSV;


use Tagcade\Model\User\Role\PublisherInterface;

interface AdSlotImporterInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param $filename
     * @param $outputFileName
     * @param $headerPosition
     * @param $csvSeparator
     * @return array
     */
    public function importCsvForPublisher(PublisherInterface $publisher, $filename, $outputFileName, $headerPosition = 0, $csvSeparator = ',');

    /**
     * @param PublisherInterface $publisher
     * @param $filename
     * @param int $headerPosition
     * @param string $csvSeparator
     * @return array
     */
    public function dumpChangesFromCsvForPublisher(PublisherInterface $publisher, $filename, $headerPosition = 0, $csvSeparator = ',');
}