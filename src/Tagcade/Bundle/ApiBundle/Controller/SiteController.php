<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Tagcade\Entity\Site;
use Tagcade\Bundle\ApiBundle\Form\Type\SiteType;

/**
 * @Security("has_role('ROLE_PUBLISHER') or has_role('ROLE_PUBLISHER_SUB_ACCOUNT')")
 */
class SiteController extends FOSRestController
{
    public function postSitesAction(Request $request)
    {
        $form = $this->createForm(new SiteType());

        $form->submit($request->request->all(), false);

        if ($form->isValid()) {

            // add publisher, persist

            return $form->getData();
        }

        return $form;
    }

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