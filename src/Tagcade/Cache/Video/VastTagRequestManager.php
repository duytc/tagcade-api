<?php


namespace Tagcade\Cache\Video;

use Redis;
use Tagcade\Exception\InvalidArgumentException;

class VastTagRequestManager implements VastTagRequestManagerInterface
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    const TOTAL_RECORD = 'totalRecord';
    const RECORDS = 'records';
    const ITEM_PER_PAGE = 'itemsPerPage';
    const CURRENT_PAGE = 'currentPage';

    /**
     * @var Redis
     */
    private $redis;

    private $host;

    private $port;
    /**
     * @var string
     */
    private $vastTagRequestNamespace;

    function __construct($host, $port, $vastTagRequestNamespace)
    {
        $this->host = $host;
        $this->port = $port;

        $this->vastTagRequestNamespace = $vastTagRequestNamespace;
    }

    /**
     * @return Redis|null
     */
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
                $this->redis = null;
            }
        }

        return $this->redis;
    }

    /**
     * @param integer $uuid current user
     * @param integer $currentPage result
     * @param integer $itemPerPage number results per page
     * @return mixed
     */
    public function getVastTagHistory($uuid, $currentPage, $itemPerPage)
    {
        if ($currentPage <= 0 || $itemPerPage <= 0){
            throw new InvalidArgumentException("Query parameter must be positive");
        }

        $redisList = sprintf($this->vastTagRequestNamespace, $uuid);
        $totalRecord = $this->getRedis()->llen($redisList);

        $from = $itemPerPage*($currentPage - 1);
        $to = $itemPerPage*$currentPage - 1;

        $rawVastTags = $this->getRedis()->lRange($redisList, $from, $to);

        if (empty($rawVastTags)) {
            return [self::TOTAL_RECORD => 0, self::RECORDS => [], self::ITEM_PER_PAGE => $itemPerPage, self::CURRENT_PAGE => $currentPage];
        }

        $records = array();
        foreach ($rawVastTags as $rawVastTag) {
            $vastTag = json_decode($rawVastTag, true);
            if (json_last_error() == JSON_ERROR_NONE){
                $records[] = $vastTag;
            }
        }
        return [self::TOTAL_RECORD => $totalRecord, self::RECORDS => $records, self::ITEM_PER_PAGE => $itemPerPage, self::CURRENT_PAGE => $currentPage];
    }
}