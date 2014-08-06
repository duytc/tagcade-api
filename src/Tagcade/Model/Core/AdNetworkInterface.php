<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface AdNetworkInterface extends ModelInterface
{
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
    public function getUrl();

    /**
     * @param string $url
     * @return self
     */
    public function setUrl($url);
}