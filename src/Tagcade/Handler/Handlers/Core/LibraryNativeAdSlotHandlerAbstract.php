<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\LibraryNativeAdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;

abstract class LibraryNativeAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return LibraryNativeAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    public function post(array $parameters)
    {
        // 1. get list site ids and channel ids (need to deploy ad slot to), also remove from params
        $sites = array_key_exists('sites', $parameters) ? $parameters['sites'] : null;
        $channels = array_key_exists('channels', $parameters) ? $parameters['channels'] : null;

        if (array_key_exists('sites', $parameters)) unset($parameters['sites']);
        if (array_key_exists('channels', $parameters)) unset($parameters['channels']);

        // 2. normal create library ad slot
        /** @var LibraryNativeAdSlotInterface $slotLibrary */
        $slotLibrary = parent::post($parameters);

        // 3. create links to sites from this library ad slot
        if (is_array($sites) || is_array($channels)) {
            $this->getDomainManager()->generateAdSlotFromLibraryForChannelsAndSites($slotLibrary, $channels, $sites);
        }

        return $slotLibrary;
    }
}
