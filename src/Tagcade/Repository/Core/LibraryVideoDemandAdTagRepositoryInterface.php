<?php


namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface LibraryVideoDemandAdTagRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param null|int $limit
     * @param null|int $offset
     * @return mixed
     */
    public function getLibraryVideoDemandAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     * @param null|int $limit
     * @param null|int $offset
     * @return mixed
     */
    public function getLibraryVideoDemandAdTagsForDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam|null $param
     * @return mixed
     */
    public function getLibraryDemandTagsForUserWithPagination(UserRoleInterface $user, PagerParam $param = null);
}