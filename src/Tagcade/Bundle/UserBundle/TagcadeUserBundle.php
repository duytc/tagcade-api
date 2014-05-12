<?php

namespace Tagcade\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TagcadeUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
