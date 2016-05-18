<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Core\SubPublisherPartnerRevenueInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Core\AdNetworkPartnerRepositoryInterface;


/**
 * @Rest\RouteResource("Partner")
 */
class PartnerController extends FOSRestController implements ClassResourceInterface
{
    const REVENUE_OPTION_NONE = 0;
    const REVENUE_OPTION_CPM_FIXED = 1;
    const REVENUE_OPTION_CPM_PERCENT = 2;

    /**
     * Get all ad network partners
     *
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="all", nullable=true)
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
    public function cgetAction()
    {
        /**
         * @var AdNetworkPartnerRepositoryInterface $adNetworkPartnerRepository
         */
        $adNetworkPartnerRepository = $this->get('tagcade.repository.ad_network_partner');

        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $publisher = $paramFetcher->get('publisher');
        $all = $paramFetcher->get('all');
        $all = filter_var($all, FILTER_VALIDATE_BOOLEAN);

        $currentUser = $this->getUser();

        $findForUser = $currentUser;

        if ($publisher !== null && ($currentUser instanceof AdminInterface || $currentUser->getId() == $publisher)) {
            /**
             * @var PublisherManagerInterface $publisherManager
             */
            $publisherManager = $this->get('tagcade_user.domain_manager.publisher');
            $publisher = $publisherManager->findPublisher($publisher);

            if (!$publisher instanceof PublisherInterface) {
                throw new NotFoundHttpException(sprintf('Not found that publisher', $publisher));
            }

            $findForUser = $publisher;
        }

        if ($all == true) {
            return $adNetworkPartnerRepository->findUnusedPartnersForPublisher($findForUser);
        }

        return $adNetworkPartnerRepository->findByUserRole($findForUser);
    }


    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\QueryParam(name="publisher", nullable=false)
     * @Rest\QueryParam(name="tagId", nullable=true)
     * @Rest\QueryParam(name="tagSize", nullable=true)
     *
     * @param $partnerId
     * @return \FOS\RestBundle\View\View
     */
    public function cgetTctagsAction($partnerId)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        /**
         * @var AdNetworkPartnerRepositoryInterface $adNetworkPartnerRepository
         */
        $adNetworkPartnerRepository = $this->get('tagcade.repository.ad_network_partner');
        $partner = $adNetworkPartnerRepository->find($partnerId);

        if (!$partner instanceof AdNetworkPartnerInterface) {
            throw new NotFoundHttpException(sprintf('Not found that partner %d', $partnerId));
        }

        $publisherId = $paramFetcher->get('publisher');
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException(sprintf('Not found that publisher %d', $publisherId));
        }

        $partnerTagId = $paramFetcher->get('tagId');
        $partnerTagSize = $paramFetcher->get('tagSize');

        if (empty($partnerTagId) && empty($partnerTagSize)) {
            return $this->view('expect either partner tag id or partner tag size', 400);
        }

        return $this->get('tagcade_app.service.core.ad_tag.partner_tag_id_finder')->getTcTag($partner, $publisher, $partnerTagId, $partnerTagSize);
    }

    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\QueryParam(name="publisher", nullable=false)
     * @Rest\QueryParam(name="domain", nullable=false)
     *
     * @param $partnerId
     * @return \FOS\RestBundle\View\View
     */
    public function cgetSubpublishersAction($partnerId)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        /**
         * @var AdNetworkPartnerRepositoryInterface $adNetworkPartnerRepository
         */
        $adNetworkPartnerRepository = $this->get('tagcade.repository.ad_network_partner');
        $partner = $adNetworkPartnerRepository->find($partnerId);

        if (!$partner instanceof AdNetworkPartnerInterface) {
            throw new NotFoundHttpException(sprintf('Not found that partner %d', $partnerId));
        }

        $publisherId = $paramFetcher->get('publisher');
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException(sprintf('Not found that publisher %d', $publisherId));
        }

        $domain = $paramFetcher->get('domain');

        $adNetworkService = $this->get('tagcade_app.service.core.ad_network.ad_network_service');
        $sites = $adNetworkService->getSitesForPartnerFilterPublisherAndDomain($partner, $publisher, $domain);

        $subPublishers = [];
        foreach ($sites as $st) {
            /**
             * @var SiteInterface $st
             */
            if (!$st->getSubPublisher() instanceof SubPublisherInterface) {
                continue;
            }

            $subPublisher = $st->getSubPublisher();
            if (!in_array($subPublisher->getId(), $subPublishers)) {
                $revenueShareConfig = $this->getRevenueShareConfigForSubPublisherAndNetworkPartner($subPublisher, $partner);
                $subPublishers[] = [
                    'id' => $subPublisher->getId(),
                    'revenueConfig' => $revenueShareConfig
                ];
            }
        }

        return $subPublishers;
    }

    protected function getRevenueShareConfigForSubPublisherAndNetworkPartner(SubPublisherInterface $subPublisher, AdNetworkPartnerInterface $partner)
    {
        $revenueShareConfig['option'] = self::REVENUE_OPTION_NONE; //default
        $revenueShareConfig['value'] = 0;

        foreach ($subPublisher->getSubPublisherPartnerRevenue() as $partnerRevenueConfig) {
            /**
             * @var SubPublisherPartnerRevenueInterface $partnerRevenueConfig
             */
            $networkPartner = $partnerRevenueConfig->getAdNetworkPartner();
            if (!$networkPartner instanceof AdNetworkPartnerInterface || $networkPartner->getId() != $partner->getId()) {
                continue;
            }

            $revenueShareConfig['option'] = $partnerRevenueConfig->getRevenueOption();
            $revenueShareConfig['value'] = $partnerRevenueConfig->getRevenueValue();
        }

        return $revenueShareConfig;
    }
}
