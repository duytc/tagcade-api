<?php

namespace Tagcade\Bundle\ApiBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Model\Core\AdTagInterface;


class AccountController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Get all active ad tags belonging to this ad network and site and publisher
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param $id
     * @param $adNetworkId
     * @param $siteId
     * @return AdTagInterface[]
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAdnetworkSiteAdtagsActiveAction($id, $adNetworkId, $siteId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($id);
        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($adNetworkId);
        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        $site = $this->get('tagcade.domain_manager.site')->find($siteId);
        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adNetwork) || false === $this->get('security.context')->isGranted('edit', $site)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        return $this->get('tagcade.domain_manager.ad_tag')->getAdTagsForAdNetworkAndSiteFilterPublisher($adNetwork, $site, $publisher);
    }
} 