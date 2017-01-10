<?php


namespace Tagcade\Cache\Video;

use Redis;
use Tagcade\Exception\InvalidArgumentException;

class VastTagRequestManager implements VastTagRequestManagerInterface
{
    const TOTAL_RECORD = 'totalRecord';
    const RECORDS = 'records';
    const ITEM_PER_PAGE = 'itemsPerPage';
    const CURRENT_PAGE = 'currentPage';

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var string
     */
    private $vastTagRequestNamespace;

    function __construct($host, $port, $vastTagRequestNamespace)
    {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
        $this->vastTagRequestNamespace = $vastTagRequestNamespace;
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
        $totalRecord = $this->redis->llen($redisList);

        $from = $itemPerPage*($currentPage - 1);
        $to = $itemPerPage*$currentPage - 1;

        $rawVastTags = $this->redis->lRange($redisList, $from, $to);

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