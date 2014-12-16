<?php

namespace Tagcade\Domain\DTO;

class ActionLogs
{
    protected $totalRecord;

    protected $logsList;

    function __construct($totalRecord, $logsList)
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
     * @return array
     */
    public function getLogsList()
    {
        return $this->logsList;
    }


}