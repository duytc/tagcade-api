<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Tagcade\Bundle\CoreBundle\Entity\Site;
use FOS\RestBundle\Controller\Annotations\View;

class SiteController extends FOSRestController
{
    /**
     */
    public function getSiteAction(Site $site)
    {
        return [
            'site' => $site
        ];
    }
}