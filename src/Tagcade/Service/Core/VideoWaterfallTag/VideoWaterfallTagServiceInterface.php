<?php


namespace Tagcade\Service\Core\VideoWaterfallTag;


use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

interface VideoWaterfallTagServiceInterface
{
    /**
     * @param LibraryVideoDemandAdTagInterface $demandAdTag
     * @return VideoWaterfallTagInterface[]
     */
    public function getValidVideoWaterfallTagsForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $demandAdTag);
}