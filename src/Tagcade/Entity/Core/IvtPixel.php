<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\IvtPixel as IvtPixelModel;
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class IvtPixel extends IvtPixelModel
{
    protected $id;
    protected $name;
    protected $urls;
    protected $fireOn;
    protected $runningLimit;

    /** @var PublisherInterface */
    protected $publisher;

    /** @var IvtPixelWaterfallTagInterface[] */
    protected $ivtPixelWaterfallTags;

    /**
     * call parent construct for default in form
     */
    function __construct()
    {
        parent::__construct();
    }
}