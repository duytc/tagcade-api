<?php


namespace Tagcade\Model;


interface RTBEnabledInterface
{
    const RTB_ENABLED = 1;
    const RTB_DISABLED = 0;
    const RTB_INHERITED = 2;

    /**
     * @return mixed
     */
    public function isRTBEnabled();
}