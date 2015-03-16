<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface SiteInterface extends ModelInterface
{
    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $domain
     * @return self
     */
    public function setDomain($domain);

    /**
     * @return string|null
     */
    public function getDomain();

    /**
     * @return PublisherInterface|null
     */
    public function getPublisher();

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return ArrayCollection
     */
    public function getAdSlots();

    /**
     * @return boolean|null
     */
    public function getEnableSourceReport();

    /**
     * @param boolean $enableSourceReport
     */
    public function setEnableSourceReport($enableSourceReport);

    public function getSourceReportSiteConfigs();
}
