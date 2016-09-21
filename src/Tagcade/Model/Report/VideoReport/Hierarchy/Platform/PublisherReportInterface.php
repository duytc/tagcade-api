<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\SubReportInterface;
use Tagcade\Model\Report\VideoReport\SuperReportInterface;

interface PublisherReportInterface extends CalculatedReportInterface, SuperReportInterface, SubReportInterface
{
    /**
     * @return VideoPublisherInterface
     */
    public function getVideoPublisher();

    /**
     * @return int|null
     */
    public function getVideoPublisherId();

    /**
     * @param VideoPublisherInterface $publisher
     * @return self
     */
    public function setVideoPublisher(VideoPublisherInterface $publisher);
} 