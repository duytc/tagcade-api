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

    protected function validateParameters($filename, &$csvSeparator)
    {
        if (!file_exists($filename) || !is_file($filename)) {
            throw new FileNotFoundException(sprintf('That file does not exists. Please recheck again this path %s', $filename));
        }

        $valid = $this->isEmptyFile($filename, $csvSeparator);

        if (!$valid) {
            throw new InvalidArgumentException('The file have no domain');
        }
    }

    protected function isEmptyFile($filename, $csvSeparator = ',')
    {
        $handle = fopen($filename, "r");

        if ($handle === FALSE) {
            return FALSE;
        }

        $row = 0;

        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            if (!empty($data[0])) {
                $row ++;
            }
        }

        $matched = false;
        if ($row > 0) {
            $matched = true;
        }

        fclose($handle);

        return $matched;
    }

    protected function adjustDomainPart($value)
    {
        return explode(' ', $value)[0];
    }
}