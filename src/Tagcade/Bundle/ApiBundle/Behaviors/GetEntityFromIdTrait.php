<?php

namespace Tagcade\Bundle\ApiBundle\Behaviors;


use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\DomainManager\ChannelManagerInterface;
use Tagcade\DomainManager\ManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;

trait GetEntityFromIdTrait
{
    /**
     * get channel(s) from id(s)
     *
     * @param mixed $ids one or array of channel ids
     * @return array|ChannelInterface[]
     */
    protected function getChannels($ids)
    {
        $myIds = $this->convertInputToArray($ids);

        /** @var ChannelManagerInterface $channelManager */
        $channelManager = $this->get('tagcade.domain_manager.channel');

        return $this->createEntitiesObject($channelManager, $myIds, ChannelInterface::class);
    }

    /**
     * get site(s) from id(s)
     *
     * @param mixed $ids one or array of site ids
     * @return array|SiteInterface[]
     */
    protected function getSites($ids)
    {
        $myIds = $this->convertInputToArray($ids);

        /** @var SiteManagerInterface $siteManager */
        $siteManager = $this->get('tagcade.domain_manager.site');

        return $this->createEntitiesObject($siteManager, $myIds, SiteInterface::class);
    }

    /**
     * create entity objects from manager and ids with expected class
     *
     * @param ManagerInterface $manager
     * @param array $ids
     * @param string $class
     * @return array|ModelInterface[]
     */
    private function createEntitiesObject(ManagerInterface $manager, array $ids, $class)
    {
        $entities = [];

        foreach ($ids as $id) {
            $entity = $manager->find($id);

            if (!is_a($entity, $class)) {
                throw new NotFoundHttpException(sprintf('entity %s with id %d is not found', $class, $id));
            }

            if (!in_array($entity, $entities)) {
                $this->checkUserPermission($entity, 'edit');
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    /**
     * convert an input to array
     *
     * @param mixed $ids one or array
     * @return array
     */
    private function convertInputToArray($ids)
    {
        if (is_numeric($ids) && $ids < 1) {
            throw new InvalidArgumentException('Expect a positive integer or array');
        }

        return !is_array($ids) ? [$ids] : $ids;
    }

    /**
     * check user permission for an entity
     *
     * @param ModelInterface $entity
     * @param string $permission
     * @return mixed
     */
    protected abstract function checkUserPermission($entity, $permission = 'view');

    /**
     * Get service instance, this should be called in a controller or a container-aware service which has container to get a service by id
     *
     * @param $id
     * @return mixed
     */
    public abstract function get($id);
}