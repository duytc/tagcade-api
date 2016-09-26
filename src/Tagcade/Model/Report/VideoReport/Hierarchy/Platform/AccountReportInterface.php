<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\SubReportInterface;
use Tagcade\Model\Report\VideoReport\SuperReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

interface AccountReportInterface extends CalculatedReportInterface, SuperReportInterface, SubReportInterface
{
    /**
     * @return UserEntityInterface
     */
    public function getPublisher();

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);
} 