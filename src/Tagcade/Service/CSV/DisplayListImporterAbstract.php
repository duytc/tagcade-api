<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Tagcade\Cache\Legacy\DisplayDomainListManager;
use Tagcade\Service\StringUtilTrait;

abstract class DisplayListImporterAbstract implements ListImporterInterface
{
    use StringUtilTrait;

    const CSV_SEPARATOR = ',';
    const DOMAIN = 0;

    /**
     * @var DisplayDomainListManager
     */
    protected $domainListManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(DisplayDomainListManager $domainListManager, LoggerInterface $logger)
    {
        $this->domainListManager = $domainListManager;
        $this->logger = $logger;
    }

    protected function adjustDomainPart($value)
    {
        return explode(' ', $value)[0];
    }
}