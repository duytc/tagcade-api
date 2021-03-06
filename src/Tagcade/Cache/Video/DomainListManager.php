<?php


namespace Tagcade\Cache\Video;

use Redis;
use Tagcade\Model\Core\BlacklistInterface;
use Tagcade\Model\Core\WhiteListInterface;

class DomainListManager implements DomainListManagerInterface
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    /**
     * @var Redis
     */
    private $redis;

    private $host;

    private $port;

    private $blackListPrefix;

    private $whiteListPrefix;

    function __construct($host, $port, $blackListPrefix, $whiteListPrefix)
    {
        $this->host = $host;
        $this->port = $port;
        $this->blackListPrefix = $blackListPrefix;
        $this->whiteListPrefix = $whiteListPrefix;
    }

    public function getRedis()
    {
        if (!$this->redis instanceof Redis) {
            $redis = new Redis();

            try {
                $redis->connect($this->host, $this->port, self::REDIS_CONNECT_TIMEOUT_IN_SECONDS);
                $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                $this->redis = $redis;
            } catch (\RedisException $e) {
                // todo refactor to check if redis is connected or not
                $this->redis= null;
            }
        }

        return $this->redis;
    }

    public function saveBlacklist(BlacklistInterface $blacklist)
    {
        $key = sprintf('%s:%s', $this->blackListPrefix, $blacklist->getSuffixKey());

        $this->getRedis()->del($key);
        $domains = $blacklist->getDomains();

        foreach($domains as $domain) {
            $this->getRedis()->sAdd($key, $domain);
        }
    }

    public function saveWhiteList(WhiteListInterface $whiteList)
    {
        $key = sprintf('%s:%s', $this->whiteListPrefix, $whiteList->getSuffixKey());

        $this->getRedis()->del($key);
        $domains = $whiteList->getDomains();

        foreach($domains as $domain) {
            $this->getRedis()->sAdd($key, $domain);
        }
    }

    public function getDomainsForBlacklist($suffixKey)
    {
        $key = sprintf('%s:%s', $this->blackListPrefix, $suffixKey);

        return $this->getRedis()->sMembers($key);
    }
}