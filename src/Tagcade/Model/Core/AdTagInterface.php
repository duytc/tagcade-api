<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface AdTagInterface extends ModelInterface
{
    /**
     * @return AdSlotInterface|null
     */
    public function getAdSlot();

    /**
     * @param AdSlotInterface $adSlot
     * @return self
     */
    public function setAdSlot(AdSlotInterface $adSlot);

    /**
     * @return AdNetworkInterface|null
     */
    public function getAdNetwork();

    /**
     * @param AdNetworkInterface $adNetwork
     * @return self
     */
    public function setAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getHtml();

    /**
     * @param string $html
     * @return self
     */
    public function setHtml($html);

    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * @param int $position
     * @return self
     */
    public function setPosition($position);
}