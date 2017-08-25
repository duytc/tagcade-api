<?php

namespace Tagcade\Model\Core;

class IvtPixelWaterfallTag implements IvtPixelWaterfallTagInterface
{
    protected $id;

    /** @var VideoWaterfallTagInterface */
    protected $waterfallTag;

    /** @var  IvtPixelInterface */
    protected $ivtPixel;

    function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getWaterfallTag()
    {
        return $this->waterfallTag;
    }

    /**
     * @inheritdoc
     */
    public function setWaterfallTag(VideoWaterfallTagInterface $waterfallTag)
    {
        $this->waterfallTag = $waterfallTag;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIvtPixel()
    {
        return $this->ivtPixel;
    }

    /**
     * @inheritdoc
     */
    public function setIvtPixel(IvtPixelInterface $ivtPixel)
    {
        $this->ivtPixel = $ivtPixel;

        return $this;
    }
}