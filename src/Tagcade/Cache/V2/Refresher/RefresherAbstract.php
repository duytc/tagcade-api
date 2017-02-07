<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Worker\Manager;

abstract class RefresherAbstract
{
    const NAMESPACE_RON_AD_SLOT_CACHE_KEY = 'tagcade_ron_adslot_v2_%d';
    const NAMESPACE_CACHE_KEY = 'tagcade_adslot_v2_%d';
    const CACHE_KEY_AD_SLOT = 'all_tags_array';

    /**
     * @var NamespaceCacheInterface
     */
    protected $cache;
    /**
     * @var Manager
     */
    private $workerManager;

    function __construct($cache, Manager $workerManager)
    {
        $this->cache = $cache;
        $this->workerManager = $workerManager;
    }

    public function refreshForCacheKey($cacheKey, ModelInterface $model)
    {
        $this->cache->setNamespace($this->getNamespaceByEntity($model));

        $oldVersion = (int)$this->cache->getNamespaceVersion();
        $newVersion = $oldVersion + 1;

        // create the new version of the cache first
        $this->cache->setNamespaceVersion($newVersion);
        $this->cache->save($cacheKey, $this->createCacheDataForEntity($model));
        $this->cache->deleteAll();

        return $this;
    }

    public function removeCacheKey($cacheKey, ModelInterface $model)
    {
        $this->cache->setNamespace($this->getNamespaceByEntity($model));
        $this->cache->delete($cacheKey);
    }

    protected function getNamespaceByEntity(ModelInterface $model)
    {
        if ($model instanceof RonAdSlotInterface) {
            return sprintf(self::NAMESPACE_RON_AD_SLOT_CACHE_KEY, $model->getId());
        }

        return $this->getNamespace($model->getId());
    }

    public function getNamespace($slotId)
    {
        return sprintf(self::NAMESPACE_CACHE_KEY, $slotId);
    }

    protected abstract function createCacheDataForEntity(ModelInterface $entity);
}