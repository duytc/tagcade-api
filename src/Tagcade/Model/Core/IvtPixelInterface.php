<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface IvtPixelInterface extends ModelInterface
{
    const ID = 'id';
    const NAME = 'name';
    const PIXELS = 'pixels';
    const FIRE_ON = 'fireOn';
    const RUNNING_LIMIT = 'runningLimit';
    const IVT_PIXEL_CONFIGS = 'ivtPixelConfigs';

    /**
     * @return PublisherInterface|null
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

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return array
     */
    public function getUrls();

    /**
     * @param array $urls
     * @return self
     */
    public function setUrls($urls);

    /**
     * @return int
     */
    public function getRunningLimit();

    /**
     * @param int $runningLimit
     * @return self
     */
    public function setRunningLimit($runningLimit);
    /**
     * @return string
     */
    public function getFireOn();

    /**
     * @param string $fireOn
     * @return self
     */
    public function setFireOn($fireOn);

    /**
     * @return mixed
     */
    public function getIvtPixelWaterfallTags();

    /**
     * @param mixed $ivtPixelWaterfallTags
     * @return self
     */
    public function setIvtPixelWaterfallTags($ivtPixelWaterfallTags);
}