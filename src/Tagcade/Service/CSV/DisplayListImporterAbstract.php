<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Tagcade\Cache\V2\DisplayBlacklistCacheManager;
use Tagcade\Cache\V2\DisplayBlacklistManager;
use Tagcade\Service\StringUtilTrait;

abstract class DisplayListImporterAbstract implements ListImporterInterface
{
    use StringUtilTrait;

    const CSV_SEPARATOR = ',';
    const DOMAIN = 0;

    /**
     * @var DisplayBlacklistCacheManager
     */
    protected $displayBlacklistCacheManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(DisplayBlacklistCacheManager $displayBlacklistCacheManager, LoggerInterface $logger)
    {
        $this->displayBlacklistCacheManager = $displayBlacklistCacheManager;
        $this->logger = $logger;
    }

    protected function adjustDomainPart($value)
    {
        return explode(' ', $value)[0];
    }
}