<?php


namespace Tagcade\Cache\Video;

use Redis;
use Tagcade\Model\Core\BlacklistInterface;
use Tagcade\Model\Core\WhiteListInterface;

class DomainListManager implements DomainListManagerInterface
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    const BLACK_LIST_PREFIX = 'video:domain_blacklist';
    const WHITE_LIST_PREFIX = 'video:domain_white_list';

    /**
     * @var Redis
     */
    private $redis;

    /*function __construct($redisArray)
    {
        //$ra = new Redis($redisArray);
        $hostConfig = explode(':', $redisArray[0]);
        $ra = new Redis();
        $ra->connect($hostConfig[0], $hostConfig[1]);
        $this->redis = $ra;
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    }/* NOT USE RedisArray from 20161209. Leave this code to backup */

    function __construct($host, $port)
    {
        // todo refactor, redis should be passed into the DomainListManager, passing in a host and port is not a clean design

        $redis = new Redis();

        try {
            $redis->connect($host, $port, self::REDIS_CONNECT_TIMEOUT_IN_SECONDS);
            $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
        } catch (\RedisException $e) {
            // do not let redis connection errors crash the entire program
            // todo refactor to check if redis is connected or not
        }

        $this->redis = $redis;
    }

    public function saveBlacklist(BlacklistInterface $blacklist)
    {
        $key = sprintf('%s:%s', self::BLACK_LIST_PREFIX, $blacklist->getSuffixKey());
        $this->redis->del($key);
        $domains = $blacklist->getDomains();
        foreach($domains as $domain) {
            $this->redis->sAdd($key, $domain);
        }
    }

    public function saveWhiteList(WhiteListInterface $whiteList)
    {
        $key = sprintf('%s:%s', self::WHITE_LIST_PREFIX, $whiteList->getSuffixKey());
        $this->redis->del($key);
        $domains = $whiteList->getDomains();
        foreach($domains as $domain) {
            $this->redis->sAdd($key, $domain);
        }
    }

    public function getDomainsForBlacklist($suffixKey)
    {
        $key = sprintf('%s:%s', self::BLACK_LIST_PREFIX, $suffixKey);
        return $this->redis->sMembers($key);
    }
}