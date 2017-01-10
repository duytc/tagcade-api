<?php


namespace Tagcade\Cache\Video;

interface VastTagRequestManagerInterface
{

    /**
     * @param integer $uuid current user
     * @param integer $currentPage result
     * @param integer $itemPerPage number results per page
     * @return mixed
     */
    public function getVastTagHistory($uuid, $currentPage, $itemPerPage);


}