<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators;

use Tagcade\Service\Report\VideoReport\Counter\VideoEventCounterInterface;
trait HasSubReportsTrait
{
    /**
     * @var \Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface
     */
    protected $subReportCreator;

    protected function syncEventCounterForSubReports()
    {
        $this->subReportCreator->setEventCounter($this->getEventCounter());
    }

    /**
     * @return VideoEventCounterInterface
     */
    abstract public function getEventCounter();
}