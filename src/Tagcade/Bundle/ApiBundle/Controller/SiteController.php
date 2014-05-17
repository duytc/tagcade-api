<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View;
use Tagcade\Entity\Site;

class SiteController extends FOSRestController
{
    /**
     * Get a site for a given id
     *
     * @ApiDoc(
     *  requirements={
     *      {
     *          "name"="site",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The site id"
     *      }
     *  },
     *  statusCodes={
     *  200 = "Returned when successful",
     *  404 = "Returned when the site is not found"
     * })
     */
    public function getSiteAction(Site $site)
    {
        return $site;
    }

    /**
     * Get all sites for the current publisher
     *
     * @ApiDoc()
     * @View()
     */
    public function getSitesAction()
    {
        // just for testing
        return $this->getDoctrine()->getManager()->getRepository('TagcadeEntity:Site')->findAll();
    }
}