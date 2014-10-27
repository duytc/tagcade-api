<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;

class Platform implements ReportTypeInterface
{
    /**
     * @var PublisherInterface[]
     */
    protected $publishers;

    public function __construct(array $publishers)
    {
        foreach($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                throw new InvalidArgumentException('parameter must be an array of publishers');
            }

            //if ($publisher->getUser()->hasDisplayModule()) {
                $this->publishers[] = $publisher;
            //}
        }
    }

    public function getPublishers()
    {
        return $this->publishers;
    }
}