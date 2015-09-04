<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface ChannelSiteInterface extends ModelInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return ChannelInterface
     */
    public function getChannel();

    /**
     * @param ChannelInterface $channel
     */
    public function setChannel($channel);

    /**
     * @return SiteInterface
     */
    public function getSite();

    /**
     * @param SiteInterface $site
     */
    public function setSite($site);

    /**
     * @return mixed
     */
    public function getDeletedAt();
}
