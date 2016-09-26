<?php


namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface VideoPublisherRepositoryInterface extends ObjectRepository
{
    /**
     * get all VideoPublishers For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array|VideoPublisherInterface[]
     */
    public function getVideoPublishersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * Get all VideoPublishers by filter parameter
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     */
    public function getVideoPublishersByFilterParams(FilterParameterInterface $filterParameter);

}