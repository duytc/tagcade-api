<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Exception\LogicException;
use Tagcade\Handler\Handlers\Core\VideoDemandPartnerHandlerAbstract;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class VideoDemandPartnerHandler extends VideoDemandPartnerHandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof PublisherInterface;
    }

    /**
     * @inheritdoc
     * @return PublisherInterface
     * @throws LogicException
     */
    public function getUserRole()
    {
        $role = parent::getUserRole();

        if (!$role instanceof PublisherInterface) {
            throw new LogicException('userRole does not implement PublisherInterface');
        }

        return $role;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->getDomainManager()->getVideoDemandPartnersForPublisher($this->getUserRole(), $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    protected function processForm(ModelInterface $videoDemandPartner, array $parameters, $method = "PUT")
    {
        /** @var VideoDemandPartnerInterface $videoDemandPartner */
        if (null == $videoDemandPartner->getPublisher()) {
            $videoDemandPartner->setPublisher($this->getUserRole());
        }

        return parent::processForm($videoDemandPartner, $parameters, $method);
    }
}