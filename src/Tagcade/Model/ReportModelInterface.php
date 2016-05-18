<?php

namespace Tagcade\Model;


use Tagcade\Model\User\Role\SubPublisherInterface;

interface ReportModelInterface {

    public function getCommonReportTagId();
    /**
     * @return mixed
     */
    public function getSubPublisher();
    /**
     * @param SubPublisherInterface $subPublisher
     */
    public function setSubPublisher($subPublisher);
}