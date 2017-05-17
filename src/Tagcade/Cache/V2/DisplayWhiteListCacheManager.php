<?php


namespace Tagcade\Cache\V2;

use Redis;
use Tagcade\Model\Core\DisplayWhiteListInterface;

class DisplayWhiteListCacheManager implements DisplayWhiteListCacheManagerInterface
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    /**
     * @var Redis
     */
    private $redis;

    private $host;

    private $port;

    private $whiteListPrefix;

    function __construct($host, $port, $whiteListPrefix)
    {
        $this->host = $host;
        $this->port = $port;
        $this->whiteListPrefix = $whiteListPrefix;
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

    public function saveWhiteList(DisplayWhiteListInterface $whiteList, array $refinedDomains = null)
    {
        $key = sprintf('%s:%s', $this->whiteListPrefix, $whiteList->getId());

        $this->getRedis()->del($key);
        $domains = $refinedDomains === null ? $whiteList->getDomains() : $refinedDomains;

        foreach ($domains as $domain) {
            $this->getRedis()->sAdd($key, $domain);
        }
    }

    /**
     * @param DisplayWhiteListInterface $whiteList
     * @return mixed
     */
    public function deleteWhiteList(DisplayWhiteListInterface $whiteList)
    {
        $key = sprintf('%s:%s', $this->whiteListPrefix, $whiteList->getId());

        return $this->getRedis()->del($key);
    }

    public function getDomainsForWhiteList($displayWhiteListId)
    {
        $key = sprintf('%s:%s', $this->whiteListPrefix, $displayWhiteListId);

        return $this->getRedis()->sMembers($key);
    }
}