<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\Core\IvtPixelWaterfallTag as IvtPixelWaterfallTagModel;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

class IvtPixelWaterfallTag extends IvtPixelWaterfallTagModel
{
    protected $id;

    /** @var  VideoWaterfallTagInterface */
    protected $waterfallTag;

    /** @var  IvtPixelInterface */
    protected $ivtPixel;
}