<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface IvtPixelWaterfallTagInterface extends ModelInterface
{
    /**
     * @return VideoWaterfallTagInterface
     */
    public function getWaterfallTag();

    /**
     * @param VideoWaterfallTagInterface $waterfallTag
     * @return self
     */
    public function setWaterfallTag(VideoWaterfallTagInterface $waterfallTag);

    /**
     * @return IvtPixelInterface
     */
    public function getIvtPixel();

    /**
     * @param IvtPixelInterface $ivtPixel
     * @return self
     */
    public function setIvtPixel(IvtPixelInterface $ivtPixel);
}