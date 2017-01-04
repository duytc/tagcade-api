<?php


namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
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
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getVideoPublishersForPublisherWithPagination(UserRoleInterface $user, PagerParam $param);

    /**
     * Get all VideoPublishers by filter parameter
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     */
    public function getVideoPublishersByFilterParams(FilterParameterInterface $filterParameter);

}