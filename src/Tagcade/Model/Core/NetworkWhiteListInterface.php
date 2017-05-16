<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface NetworkWhiteListInterface extends ModelInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork();

    /**
     * @param AdNetworkInterface $adNetwork
     */
    public function setAdNetwork($adNetwork);

    /**
     * @return DisplayWhiteListInterface
     */
    public function getDisplayWhiteList();

    /**
     * @param DisplayWhiteListInterface $displayWhiteList
     */
    public function setDisplayWhiteList($displayWhiteList);
}