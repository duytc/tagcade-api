<?php


namespace Tagcade\Cache\V2;

use Redis;
use Tagcade\Model\Core\DisplayBlacklistInterface;

class DisplayBlacklistCacheManager implements DisplayBlacklistCacheManagerInterface
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    /**
     * @var Redis
     */
    private $redis;

    private $host;

    private $port;

    private $blackListPrefix;

    function __construct($host, $port, $blackListPrefix)
    {
        $this->host = $host;
        $this->port = $port;
        $this->blackListPrefix = $blackListPrefix;
    }

    public function getRedis()
    {
        if (!$this->redis instanceof Redis) {
            $redis = new Redis();

            try {
                $redis->connect($this->host, $this->port, self::REDIS_CONNECT_TIMEOUT_IN_SECONDS);
//                $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                $this->redis = $redis;
            } catch (\RedisException $e) {
                // todo refactor to check if redis is connected or not
                $this->redis = null;
            }
        }

        return $this->redis;
    }

    public function saveBlacklist(DisplayBlacklistInterface $blacklist, array $refinedDomains = null)
    {
        $key = sprintf('%s:%s', $this->blackListPrefix, $blacklist->getId());

        $this->getRedis()->del($key);
        $domains = $refinedDomains === null ? $blacklist->getDomains() : $refinedDomains;

        foreach ($domains as $domain) {
            $this->getRedis()->sAdd($key, $domain);
        }
    }

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return mixed
     */
    public function deleteBlacklist(DisplayBlacklistInterface $blacklist)
    {
        $key = sprintf('%s:%s', $this->blackListPrefix, $blacklist->getId());

        return $this->getRedis()->del($key);
    }

    public function getDomainsForBlacklist($displayBlacklistId)
    {
        $key = sprintf('%s:%s', $this->blackListPrefix, $displayBlacklistId);

        return $this->getRedis()->sMembers($key);
    }
}