<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherSiteInterface extends ModelInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
     */
    public function setSubPublisher($subPublisher);

    /**
     * @return SiteInterface
     */
    public function getSite();

    /**
     * @param SiteInterface $site
     * @return self
     */
    public function setSite($site);

    /**
     * @return integer
     */
    public function getAccess();

    /**
     * @param integer $access
     * @return self
     */
    public function setAccess($access);

    /**
     * @return mixed
     */
    public function getDeletedAt();
}
