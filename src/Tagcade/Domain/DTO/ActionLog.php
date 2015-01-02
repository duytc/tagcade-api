<?php

namespace Tagcade\Domain\DTO;

class ActionLog
{
    /**
     * @var int
     */
    protected $numRows;

    /**
     * @var ActionLog[]
     */
    protected $logsList;

    function __construct($numRows, array $logsList)
    {
        $this->numRows = $numRows;
        $this->logsList = $logsList;
    }

    /**
     * @return int
     */
    public function getTotalRecord()
    {
        return $this->numRows;
    }

    /**
     * @return ActionLog[]
     */
    public function getLogsList()
    {
        return $this->logsList;
    }

}