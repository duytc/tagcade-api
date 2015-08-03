<?php

namespace Tagcade\Handler\Handlers\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\DomainManager\NativeAdSlotManagerInterface;
use Tagcade\Handler\CloneAdSlotTrait;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

abstract class NativeAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    use CloneAdSlotTrait;
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return NativeAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}