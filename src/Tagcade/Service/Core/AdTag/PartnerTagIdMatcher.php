<?php


namespace Tagcade\Service\Core\AdTag;

use Psr\Log\LoggerInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;

class PartnerTagIdMatcher implements PartnerTagIdMatcherInterface
{
    const PATTERN_KEY = 'pattern';
    const FINALIZE_PATTERN_KEY = 'finalize-pattern';
    const FIRST_PARENTHESIZED_MATCH = 1;
    const PREG_MATCH_FAILED = false;
    const PREG_NOT_MATCH = 0;

    /**
     * @var array
     */
    protected $configs;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PartnerTagIdMatcher constructor.
     * @param array $configs
     * @param LoggerInterface $logger
     */
    public function __construct(array $configs, LoggerInterface $logger)
    {
        $this->configs = $configs;
        $this->logger = $logger;
    }

    /**
     * extract the partner tag id from html description based on a pre-defined pattern
     * @param LibraryAdTagInterface $libraryAdTag
     * @return false if error occurs or configuration does not exist
     *         0 if the pattern is not matched
     *         string if partner tag id is found
     */
    public function extractPartnerTagId(LibraryAdTagInterface $libraryAdTag)
    {
        if (!$libraryAdTag->getAdNetwork()->getNetworkPartner() instanceof AdNetworkPartnerInterface) {
            return false;
        }

        $adNetworkPartner = $libraryAdTag->getAdNetwork()->getNetworkPartner();

        $partnerCName = $adNetworkPartner->getNameCanonical();
        if (!array_key_exists($partnerCName, $this->configs)) {
            $this->logger->info(sprintf('not found any matcher configuration for partner %s', $adNetworkPartner->getName()));
            return false; // The partner may require different method (manual method) to update its tag id
        }

        $config = $this->configs[$partnerCName];

        if (!isset($config[self::FINALIZE_PATTERN_KEY]) || empty($config[self::FINALIZE_PATTERN_KEY])) {
            $res = preg_match($config[self::PATTERN_KEY], $libraryAdTag->getHtml(), $matches);
            if ($res === self::PREG_MATCH_FAILED || $res === self::PREG_NOT_MATCH) {
                $this->logger->info(sprintf('could not found a partner tag id for library ad tag %d', $libraryAdTag->getId()));
                return $res;
            }

            return trim($matches[self::FIRST_PARENTHESIZED_MATCH]);
        }

        return trim(preg_replace($config[self::PATTERN_KEY], $config[self::FINALIZE_PATTERN_KEY], $libraryAdTag->getHtml()));
    }
}