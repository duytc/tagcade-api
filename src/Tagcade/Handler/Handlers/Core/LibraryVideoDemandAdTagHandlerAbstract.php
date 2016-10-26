<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\LibraryVideoDemandAdTagManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

abstract class LibraryVideoDemandAdTagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return LibraryVideoDemandAdTagManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    /**
     * @inheritdoc
     */
    public function post(array $parameters)
    {
        /* we custom 'post' here for supporting create & deploy library video demand ad tag to multi video waterfall tags */
        // 1. normal create library video demand ad tag
        /** @var LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag */
        $libraryVideoDemandAdTag = parent::post($parameters);

         // 2. create links to sites from this library ad slot
        $this->getDomainManager()->deployLibraryVideoDemandAdTag($libraryVideoDemandAdTag);
//        $this->getDomainManager()->deployLibraryVideoDemandAdTagBasedOnManualPlacementRule($libraryVideoDemandAdTag);

        return $libraryVideoDemandAdTag;
    }
}