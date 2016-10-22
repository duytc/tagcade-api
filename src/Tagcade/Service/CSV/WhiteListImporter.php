<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tagcade\Cache\Video\DomainListManager;
use Tagcade\DomainManager\WhiteListManagerInterface;
use Tagcade\Entity\Core\WhiteList;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\StringUtilTrait;

class WhiteListImporter implements WhiteListImporterInterface
{
    use StringUtilTrait;

    const CSV_SEPARATOR = ',';
    const HEADER_POSITION = 0;

    const FILE_NAME = 'input.csv';
    const DOMAIN = 0;

    protected $HEADERS = ['domain'];

    private $domainListManager;
    private $whiteListManager;
    private $logger;

    public function __construct(DomainListManager $domainListManager, WhiteListManagerInterface $whiteListManager, LoggerInterface $logger)
    {
        $this->domainListManager = $domainListManager;
        $this->whiteListManager = $whiteListManager;
        $this->logger = $logger;
    }

    public function importCsv($filename, PublisherInterface $publisher, $name, $headerPosition = null, $csvSeparator = null)
    {
        $this->validateParameters($filename, $headerPosition, $csvSeparator);

        $handle = fopen($filename, "r");
        if ($handle === FALSE) {
            return;
        }

        $row = 0;
        $whiteListDomains = [];
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            $this->logger->info(sprintf('start read row %d', $row), $data);
            if ($row <= $headerPosition) {
                $row++;
                continue;
            }

            $whiteListDomain = $this->extractDomain($this->adjustDomainPart($data[self::DOMAIN]));
            $whiteListDomains[] = $whiteListDomain;
            $this->logger->info(sprintf('finish read row %d', $row), $data);
            $row++;
        }

        $whiteList = new WhiteList();
        $whiteList->setPublisher($publisher);
        $whiteList->setName($name);
        $whiteList->setDomains($whiteListDomains);
        $this->whiteListManager->save($whiteList);

        $whiteList->setSuffixKey($whiteList->getId());
        $this->whiteListManager->save($whiteList);
        $this->domainListManager->saveWhiteList($whiteList);
    }

    private function validateParameters(&$filename, &$headerPosition, &$csvSeparator)
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

        $valid = $this->checkCSVFileFormat($filename, $this->HEADERS, $headerPosition, $csvSeparator);

        if (!$valid) {
            throw new InvalidArgumentException('The file format is invalid');
        }
    }

    private function checkCSVFileFormat($filename, array $headers, $headerRow, $csvSeparator = ',')
    {
        $handle = fopen($filename, "r");

        if ($handle === FALSE) {
            return FALSE;
        }

        $row = 0;
        $matched = false;
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            // check header is matched
            if ($row === $headerRow) {
                $matched = $this->matchRow($data, $headers);
                break;
            }

            $row ++;
        }
        fclose($handle);

        return $matched;
    }

    private function matchRow(array $rowData, array $headers)
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

    private function adjustDomainPart($value)
    {
        return explode(' ', $value)[0];
    }
}