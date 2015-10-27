<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\ModelInterface;

abstract class RefresherAbstract {

    const NAMESPACE_RON_AD_SLOT_CACHE_KEY = 'tagcade_ron_adslot_v2_%d';
    const NAMESPACE_CACHE_KEY = 'tagcade_adslot_v2_%d';
    const CACHE_KEY_AD_SLOT = 'all_tags_array';

    /**
     * @var NamespaceCacheInterface
     */
    protected $cache;

    function __construct($cache)
    {
        $this->cache = $cache;
    }

    public function refreshForCacheKey($cacheKey, ModelInterface $model)
    {
        $this->cache->setNamespace($this->getNamespaceByEntity($model));

        $oldVersion = (int)$this->cache->getNamespaceVersion();
        $newVersion = $oldVersion + 1;

        // create the new version of the cache first
        $this->cache->setNamespaceVersion($newVersion);

        $this->cache->save($cacheKey, $this->createCacheDataForEntity($model));

        // delete the old version of the cache
        $this->cache->setNamespaceVersion($oldVersion);

        $this->cache->deleteAll();

        return $this;
    }

    protected function getNamespaceByEntity(ModelInterface $model) {
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