<?php

namespace Tagcade\Cache\Video\Refresher;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Cache\CacheNamespace\RedisNamespaceCache;
use Tagcade\Entity\Core\LibraryVideoDemandAdTag;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\IvtPixel;
use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
use Tagcade\Model\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoTargetingInterface;
use Tagcade\Model\Core\VideoWaterfallTag;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class VideoWaterfallTagCacheRefresher implements VideoWaterfallTagCacheRefresherInterface
{
    const NAMESPACE_CACHE_KEY = 'video:waterfall_tag:%s:tag_config'; // using %s for uuid
    const CACHE_KEY_VIDEO_DEMAND_AD_TAG = 'all_demand_ad_tags_array';
    /**
     * @var RedisNamespaceCache
     */
    private $cacheNamespace;

    private $blackListPrefix;

    private $whiteListPrefix;

    function __construct(RedisNamespaceCache $cacheNamespace, $blackListPrefix, $whiteListPrefix)
    {
        $this->cacheNamespace = $cacheNamespace;
        $this->blackListPrefix = $blackListPrefix;
        $this->whiteListPrefix = $whiteListPrefix;
    }

    /**
     * @param $videoWaterfallTagId
     * @return string
     * @throws \Exception
     */
    protected function getCacheKeyOfVideoWaterfallTag($videoWaterfallTagId)
    {
        if (!is_string($videoWaterfallTagId)) {
            throw new \Exception(sprintf('Expect video ad tag id is number, got %s type ', gettype($videoWaterfallTagId)));
        }

        return sprintf(self::NAMESPACE_CACHE_KEY, $videoWaterfallTagId);
    }

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return mixed|void
     * @throws \Exception
     */
    public function refreshVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        $videoWaterfallTagId = $videoWaterfallTag->getUuid(); // using uuid instead of id
        $namespaceOfThisVideoWaterfallTag = $this->getCacheKeyOfVideoWaterfallTag($videoWaterfallTagId);
        $this->cacheNamespace->setNamespace($namespaceOfThisVideoWaterfallTag);

        $videoWaterfallTagData = $this->createCacheVideoWaterfallTagData($videoWaterfallTag);
        $this->cacheNamespace->saveDataAndIncreaseVersion(self::CACHE_KEY_VIDEO_DEMAND_AD_TAG, $videoWaterfallTagData);
    }

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return mixed|void
     */
    public function removeVideoWaterfallTagCache(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        $videoWaterfallTagId = $videoWaterfallTag->getId();
        $this->cacheNamespace->removeCache($videoWaterfallTagId);
    }

    /**
     * @param $videoWaterfallTagId
     * @return mixed|string
     * @throws \Exception
     */
    public function getCacheForVideoWaterfallTag($videoWaterfallTagId)
    {
        $namespaceOfThisVideoWaterfallTag = $this->getCacheKeyOfVideoWaterfallTag($videoWaterfallTagId);
        $namespaceVersionKey = $this->cacheNamespace->getNamespaceVersionKey($namespaceOfThisVideoWaterfallTag);
        $version = $this->cacheNamespace->doFetch($namespaceVersionKey);
        $this->cacheNamespace->setNamespaceVersion((string)($version));

        return $this->cacheNamespace->fetch($videoWaterfallTagId);
    }

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return array format as example (notice required fields):
     * [
     *      "id": "04d73b13-7673-4de7-b237-88c37f33ac7a", // uuid, required
     *      "platform": ["flash", "js"], // either flash or js, required
     *      "adDuration": 30, required
     *      "runOn": Client-Side VAST+VPAID, required
     *      "waterfall": [
     *          0: [
     *              "strategy": "parallel", // parallel or linear, required
     *              "demandTags": [ // required
     *                  0: [
     *                      "id": 383, // required
     *                      "demandPartner": "liverail", // cname, required
     *                      "tagUrl": "http://vast-dummy.tagcade.dev/dummy_tag.php?name=tag1&example=nike", // required
     *                      "weight": 50,
     *                      "priority": 10,
     *                      "httpRequestTimeout": 3000, // in milliseconds. Note: old is 'timeout' in seconds
     *                      "targeting": [
     *                          "required_macros": ["page_url"],
     *                          "player_size": ["small"],
     *                          "blacklist_domain_sets": ["my_list"]
     *                      ]
     *                  ],
     *                  ...
     *              ]
     *          ],
     *          ...
     *      ]
     *      "companionAds": []
     * ]
     */
    public function createCacheVideoWaterfallTagData(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        $data = [
            'id' => $videoWaterfallTag->getUuid(),
            'waterfallId' => $videoWaterfallTag->getId(),
            'platform' => $videoWaterfallTag->getPlatform(),
            'adDuration' => $videoWaterfallTag->getAdDuration(),
            'waterfall' => [],
            'runOn' => $videoWaterfallTag->getRunOn(),
            'companionAds' => $videoWaterfallTag->getCompanionAds(),
        ];

        $data = $this->addIvtConfigsToVideoWaterfallTagCache($data, $videoWaterfallTag);

        /** @var VideoWaterfallTagItemInterface[] $videoWaterfallTagItems */
        $videoWaterfallTagItems = $videoWaterfallTag->getVideoWaterfallTagItems();

        if (null == $videoWaterfallTagItems) {
            return $data;
        }

        if ($videoWaterfallTagItems instanceof PersistentCollection) {
            $videoWaterfallTagItems = $videoWaterfallTagItems->toArray();
        }

        // sort all videoWaterfallTagItems by position
        usort($videoWaterfallTagItems, function (VideoWaterfallTagItemInterface $a, VideoWaterfallTagItemInterface $b) {
            return $a->getPosition() > $b->getPosition();
        });

        /** @var VideoWaterfallTagItemInterface $videoWaterfallTagItem */
        foreach ($videoWaterfallTagItems as $videoWaterfallTagItem) {
            if ($videoWaterfallTagItem->getDeletedAt() !== null) {
                continue;
            }

            /*
             * dataItem = [
             *      'strategy' => 'parallel'|'linear',
             *      'demandTags' => [
             *           ...demandTagItems...
             *      ]
             * ]
             */
            $dataItem = [];

            $dataItem['strategy'] = $videoWaterfallTagItem->getStrategy();
            $dataItem['demandTags'] = [];

            $videoDemandAdTags = $videoWaterfallTagItem->getVideoDemandAdTags();

            $videoDemandAdTags = is_null($videoDemandAdTags) ? [] : $videoDemandAdTags;
            if ($videoDemandAdTags instanceof Collection) {
                $videoDemandAdTags = $videoDemandAdTags->toArray();
            }

            /** @var VideoDemandAdTagInterface $videoDemandAdTag */
            foreach ($videoDemandAdTags as $videoDemandAdTag) {
                if ($videoDemandAdTag->getActive() !== VideoDemandAdTag::ACTIVE || $videoDemandAdTag->getDeletedAt() !== null) {
                    continue;
                }

                /*
                 * $demandAdTagItem = [
                 *      "id": 383,
                 *      "demandPartner": "...",
                 *      "tagUrl": "http://vast...",
                 *      "weight": 50,
                 *      "priority": 10,
                 *      "httpRequestTimeout": 3000,
                 *      "targeting": [
                 *          "required_macros": ["page_url"...],
                 *          "player_size": ["small"...],
                 *          "blacklist_domain_sets": ["my_list"...]
                 *      ];
                 * ];
                 */
                $demandAdTagItem = [];

                /** @var VideoDemandAdTagInterface $videoDemandAdTag */
                // required fields
                $demandAdTagItem['id'] = $videoDemandAdTag->getId();
                $demandAdTagItem['demandPartner'] = $videoDemandAdTag->getVideoDemandPartner() instanceof VideoDemandPartnerInterface ? $videoDemandAdTag->getVideoDemandPartner()->getNameCanonical() : "";
                $demandAdTagItem['tagUrl'] = $videoDemandAdTag->getTagURL();

                // optional fields
                if (null != $videoDemandAdTag->getPriority()) {
                    $demandAdTagItem['priority'] = $videoDemandAdTag->getPriority();
                }

                if (null != $videoDemandAdTag->getRotationWeight()) {
                    $demandAdTagItem['weight'] = $videoDemandAdTag->getRotationWeight();
                }

                if (null != $videoDemandAdTag->getTimeout()) {
                    $demandAdTagItem['httpRequestTimeout'] = $videoDemandAdTag->getTimeout();
                }

                if (null != $videoDemandAdTag->getSellPrice()) {
                    $demandAdTagItem['sellPrice'] = $videoDemandAdTag->getSellPrice();
                }

                $demandAdTagItem[ExpressionInterface::TARGETING] = [];

                // build targeting
                $targeting = $videoDemandAdTag->getTargeting();
                $targeting = is_null($targeting) ? [] : $targeting;

                foreach (VideoDemandAdTag::getSupportedTargetingKeys() as $vdtValue) {
                    if (!array_key_exists($vdtValue, $targeting)
                        || null == $targeting[$vdtValue] || count($targeting[$vdtValue]) < 1
                    ) {
                        continue; // skip targeting element if value not set
                    }

                    if ($vdtValue === VideoTargetingInterface::TARGETING_KEY_EXCLUDE_DOMAINS) {
                        $demandAdTagItem[ExpressionInterface::TARGETING][$vdtValue] = array_map(function (array $item) {
                            return sprintf('%s:%s', $this->blackListPrefix, $item[LibraryVideoDemandAdTag::LIST_DOMAIN_SUFFIX_KEY]);
                        }, $targeting[$vdtValue]);
                        continue;
                    } else if ($vdtValue === VideoTargetingInterface::TARGETING_KEY_DOMAINS) {
                        $demandAdTagItem[ExpressionInterface::TARGETING][$vdtValue] = array_map(function (array $item) {
                            return sprintf('%s:%s', $this->whiteListPrefix, $item[LibraryVideoDemandAdTag::LIST_DOMAIN_SUFFIX_KEY]);
                        }, $targeting[$vdtValue]);
                        continue;
                    }

                    $demandAdTagItem[ExpressionInterface::TARGETING][$vdtValue] = $targeting[$vdtValue];
                }

                // also check supported and use targeting of waterfall tag
                $waterfallTagTargeting = $videoWaterfallTag->getTargeting();

                foreach (VideoWaterfallTag::getSupportedTargetingKeys() as $wftValue) {
                    if (!array_key_exists($wftValue, $waterfallTagTargeting)
                        || null == $waterfallTagTargeting[$wftValue] || count($waterfallTagTargeting[$wftValue]) < 1
                    ) {
                        continue; // skip targeting element if value not set
                    }

                    $demandAdTagItem[ExpressionInterface::TARGETING][$wftValue] = $waterfallTagTargeting[$wftValue];
                }

                array_push($dataItem['demandTags'], $demandAdTagItem);
            }

            // skip set $dataItem if demandTags is empty
            if (count($dataItem['demandTags']) < 1) {
                continue;
            }

            array_push($data['waterfall'], $dataItem);
        }

        return ($data);
    }

    /**
     * add IvtConfigs To VideoWaterfallTagCache. The output formats as:
     *
     * [
     *   <waterfall tag data...>,
     *   [
     *      'pixels' => [
     *              'http://test-ivp-pixel.com?on=request&id=1',
     *              'http://test-ivp-pixel-2.com?on=request&id=2',
     *      ],
     *      'fireOn' => 'request',
     *      'runningLimit' => 50, // only for fireOn="request"
     *   ],
     *   [
     *      'pixels' => [
     *          'http://test-ivp-pixel-3.com?on=impression&id=3',
     *      ],
     *      'fireOn' => 'impression',
     *   ]
     * ]
     * @param array $data
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return array
     */
    private function addIvtConfigsToVideoWaterfallTagCache(array $data, VideoWaterfallTagInterface $videoWaterfallTag)
    {
        $ivtWaterfallTags = $videoWaterfallTag->getIvtPixelWaterfallTags();

        if ($ivtWaterfallTags instanceof Collection) {
            $ivtWaterfallTags = $ivtWaterfallTags->toArray();
        }

        foreach ($ivtWaterfallTags as $ivtWaterfallTag) {
            if (!$ivtWaterfallTag instanceof IvtPixelWaterfallTagInterface) {
                continue;
            }

            $pixel = $ivtWaterfallTag->getIvtPixel();

            if (!$pixel instanceof IvtPixelInterface) {
                continue;
            }

            $pixelData = [
                IvtPixelInterface::PIXELS => $pixel->getUrls(),
                IvtPixelInterface::FIRE_ON => $pixel->getFireOn(),
            ];

            if ($pixel->getFireOn() == IvtPixel::FIRE_ON_REQUEST) {
                $pixelData[IvtPixelInterface::RUNNING_LIMIT] = $pixel->getRunningLimit();
            }

            $data[IvtPixelInterface::IVT_PIXEL_CONFIGS][] = $pixelData;
        }

        return $data;
    }
}