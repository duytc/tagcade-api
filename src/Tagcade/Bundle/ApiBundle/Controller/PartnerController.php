<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\AdNetworkInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Model\Core\AdNetworkPartner;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdNetworkPartnerRepositoryInterface;


/**
 * @Rest\RouteResource("Partner")
 */
class PartnerController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Get all ad network partners
     *
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     *
     * @ApiDoc(
     *  section="Partners",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return AdNetworkPartnerInterface[]
     */
    public function cgetAction(Request $request)
    {
        $user = $this->getUser();
        $publisher = $user;

        /**
         * @var AdNetworkPartnerRepositoryInterface $adNetworkPartnerRepository
         */
        $adNetworkPartnerRepository = $this->get('tagcade.repository.ad_network_partner');
        if ($this->getUser() instanceof AdminInterface) {
            $publisherId = $request->query->get('publisher', null);

            if (null === $publisherId) {
                return $adNetworkPartnerRepository->findAll();
            }

            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);
            if (!$publisher instanceof PublisherInterface) {
                throw new NotFoundHttpException('Not found that publisher');
            }
        }

        return $adNetworkPartnerRepository->findByPublisher($publisher->getId());
    }
}
