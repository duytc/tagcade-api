<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tagcade\Cache\Video\DomainListManager;
use Tagcade\Service\StringUtilTrait;

abstract class ListImporterAbstract implements ListImporterInterface
{
    use StringUtilTrait;

    const CSV_SEPARATOR = ',';
    const DOMAIN = 0;

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

    protected function adjustDomainPart($value)
    {
        return explode(' ', $value)[0];
    }
}