<?php


namespace Tagcade\Service\Core\VideoDemandAdTag;


use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

interface DeployLibraryVideoDemandAdTagServiceInterface
{
    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @param $rule
     * @param array|VideoWaterfallTagInterface[] $videoWaterfallTags
     * @param null|array $targeting
     * @param bool $targetingOverride
     * @param null|int $priority
     * @param null|int $rotationWeight
     * @param null|boolean $active
     * @param null|int $position
     * @param bool $shiftDown default false, if true => auto increase position for videoWaterfallTagItem related to created videoDemandAdTag
     * @return mixed
     */
    public function deployLibraryVideoDemandAdTagToWaterfalls(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, $rule, array $videoWaterfallTags, $targeting = null, $targetingOverride = false, $priority = null, $rotationWeight = null, $active = null, $position = null, $shiftDown = false);

    /**
     * @param LibraryVideoDemandAdTagInterface $demandAdTag
     * @return VideoWaterfallTagInterface[]
     */
    public function getValidVideoWaterfallTagsForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $demandAdTag);
}