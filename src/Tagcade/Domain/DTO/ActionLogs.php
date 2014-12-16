<?php

namespace Tagcade\Domain\DTO;

use Tagcade\Bundle\AdminApiBundle\Entity\ActionLog;

class ActionLogs
{
    /**
     * @var int
     */
    protected $totalRecord;

    /**
     * @var ActionLog[]
     */
    protected $logsList;

    function __construct($totalRecord, array $logsList)
    {
        $this->totalRecord = $totalRecord;
        $this->logsList = $logsList;
    }

    /**
     * @return int
     */
    public function getTotalRecord()
    {
        return $this->totalRecord;
    }

    /**
     * @return ActionLog[]
     */
    public function getLogsList()
    {
        return $this->logsList;
    }


}