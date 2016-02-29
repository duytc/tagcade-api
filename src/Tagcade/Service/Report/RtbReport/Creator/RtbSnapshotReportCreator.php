<?php

namespace Tagcade\Service\Report\RtbReport\Creator;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\Report\RtbReport\Counter\RtbEventCounterInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbCreatorInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorInterface;

class RtbSnapshotReportCreator extends RtbReportCreatorAbstract implements RtbSnapshotReportCreatorInterface
{
    /**
     * @param RtbCreatorInterface[] $creators
     * @param RtbEventCounterInterface $eventCounter
     */
    public function __construct(array $creators, RtbEventCounterInterface $eventCounter)
    {
        parent::__construct($creators, $eventCounter);
    }

    /**
     * @inheritdoc
     */
    protected function addCreator(RtbCreatorInterface $creator)
    {
        if (!$creator instanceof RtbSnapshotCreatorInterface) {
            throw new InvalidArgumentException('Require SnapshotCreatorInterface for SnapshotReportCreator');
        }

        parent::addCreator($creator);
    }
}