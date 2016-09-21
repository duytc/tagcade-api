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
        // 1. get list video waterfall tag ids (need to deploy library video demand ad ag to), also remove from params
        /** @var VideoWaterfallTagInterface[] $videoWaterfallTags */
        $videoWaterfallTags = array_key_exists('waterfalls', $parameters) ? $parameters['waterfalls'] : null;

        if (array_key_exists('waterfalls', $parameters)) {
            unset($parameters['waterfalls']);
        }

        // 2. normal create library video demand ad tag
        /** @var LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag */
        $libraryVideoDemandAdTag = parent::post($parameters);

        // 3. create links to sites from this library ad slot
        if (is_array($videoWaterfallTags)) {
            $this->getDomainManager()->generateVideoDemandAdTagsFromLibraryForVideoWaterfallTags($libraryVideoDemandAdTag, $videoWaterfallTags);
        }

        return $libraryVideoDemandAdTag;
    }
}