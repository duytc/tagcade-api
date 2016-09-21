<?php


namespace Tagcade\Cache\Video;

use Redis;
use RedisArray;
use Tagcade\Model\Core\BlacklistInterface;
use Tagcade\Model\Core\WhiteListInterface;

class DomainListManager implements DomainListManagerInterface
{
    const BLACK_LIST_PREFIX = 'video:domain_blacklist';
    const WHITE_LIST_PREFIX = 'video:domain_white_list';

    /**
     * @var RedisArray|\Redis
     */
    private $redis;

    function __construct($redisArray)
    {
        $ra = new RedisArray($redisArray);
        $this->redis = $ra;
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
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