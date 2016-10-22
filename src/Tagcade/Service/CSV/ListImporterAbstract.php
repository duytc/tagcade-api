<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tagcade\Cache\Video\DomainListManager;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\StringUtilTrait;

abstract class ListImporterAbstract implements ListImporterInterface
{
    use StringUtilTrait;

    const CSV_SEPARATOR = ',';
    const HEADER_POSITION = 0;

    const FILE_NAME = 'input.csv';
    const DOMAIN = 0;

    protected $HEADERS = ['domain'];

    /**
     * @var DomainListManager
     */
    protected $domainListManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(DomainListManager $domainListManager, LoggerInterface $logger)
    {
        $this->domainListManager = $domainListManager;
        $this->logger = $logger;
    }

    protected function validateParameters(&$filename, &$headerPosition, &$csvSeparator, $checkHeader = null)
    {
        if ($headerPosition === null) {
            $headerPosition = self::HEADER_POSITION;
        }

        if ($filename === null) {
            $filename = self::FILE_NAME;
        }

        if ($csvSeparator === null) {
            $csvSeparator = self::CSV_SEPARATOR;
        }

        if (!file_exists($filename) || !is_file($filename)) {
            throw new FileNotFoundException(sprintf('That file does not exists. Please recheck again this path %s', $filename));
        }

        $valid = $this->checkCSVFileFormat($filename, $this->HEADERS, $headerPosition, $csvSeparator, $checkHeader);

        if (!$valid) {
            throw new InvalidArgumentException('The file format is invalid');
        }
    }

    protected function checkCSVFileFormat($filename, array $headers, $headerRow, $csvSeparator = ',', $checkHeader = null)
    {
        $handle = fopen($filename, "r");

        if ($handle === FALSE) {
            return FALSE;
        }

        $row = 0;
        $matched = false;
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            // check header is matched
            if (!$checkHeader) {
                $matched = true;
            } else if ($row === $headerRow) {
                $matched = $this->matchRow($data, $headers);
                break;
            }

            $row ++;
        }

        if ($row <= 1) {
            throw new \Exception('There is no domain in file');
        }

        fclose($handle);

        return $matched;
    }

    protected function matchRow(array $rowData, array $headers)
    {
        $rowDataCount = count($rowData);
        $headerCount = count($headers);

        if ($rowDataCount < $headerCount) {
            return false;
        }

        if ($rowDataCount > $headerCount) {
            $rowData = array_slice($rowData, 0, $headerCount);
        }

        // canonical data before compare
        $matched = true;
        for($i = 0; $i < $headerCount; $i ++) {
            $rowItem = trim($rowData[$i]);
            $headerItem = trim($headers[$i]);

            $rowItem = str_replace('  ',' ', $rowItem);
            $rowItem = str_replace(' ','-', $rowItem);
            $headerDataItem =  str_replace(' ','-', $headerItem);

            if (strcasecmp($rowItem, $headerDataItem) != 0) {
                $matched = false;
                break;
            }

        }

        return $matched;
    }

    protected function adjustDomainPart($value)
    {
        return explode(' ', $value)[0];
    }
}