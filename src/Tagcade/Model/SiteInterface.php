<?php

namespace Tagcade\Model;

interface SiteInterface
{
    public function setName($name);
    public function getName();
    public function setDomain($domain);
    public function getDomain();
}
