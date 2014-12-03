<?php

namespace Tagcade\Legacy;


interface NamespaceCacheInterface {

    public function setNamespaceForAdSlot($adSlotId, $namespace);

    public function getNamespaceForAdSlot($adSlotId);
} 