<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Service\TagLibrary\UnlinkServiceInterface;

/**
 * @Rest\RouteResource("Adtag")
 */
class AdTagController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;
    /**
     * Get all ad tags
     * @Rest\View(
     *      serializerGroups={"adtag.detail", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "slotlib.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "site.summary", "user.summary", "adnetwork.summary", "libraryadtag.detail"}
     * )
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return AdTagInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        /**
         * @var AdTagRepositoryInterface $adTagRepository
         */
        $adTagRepository = $this->get('tagcade.repository.ad_tag');

        /**
         * @var AdTagManagerInterface $adTagManager
         */
        $adTagManager = $this->get('tagcade.domain_manager.ad_tag');

        if ($request->query->get('page') > 0) {
            $qb = $adTagRepository->getAdTagsForPublisherWithPagination($role, $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return ($role instanceof PublisherInterface)
            ? $adTagRepository->getAdTagsForPublisher($role)
            : $adTagManager->all();
    }

    /**
     * @Rest\View(
     *      serializerGroups={"adtag.detail", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "slotlib.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "site.summary", "user.summary", "adnetwork.summary", "libraryadtag.detail"}
     * )
     *
     * Get a single adTag for the given id
     *
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return AdTagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * Create a adTag from the submitted data
     *
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postAction(Request $request)
    {
        if (!array_key_exists('adSlot', $request->request->all())) {
            throw new InvalidArgumentException('Expect ad slot parameter');
        }

        $inputAdSlots = $request->request->get('adSlot');
        if (null == $inputAdSlots) {
            throw new InvalidArgumentException('Ad slot value is invalid');
        }
        $adSlotId = !is_array($inputAdSlots) ? [$inputAdSlots] : $inputAdSlots;

        /** @var BaseAdSlotInterface[] $adSlots */
        $adSlots = $this->getAdSlots($adSlotId);
        $filteredAdSlots = [];
        $libAdSlotIdArray = [];

        foreach ($adSlots as $adSlot) {
            $libAdSlotId = $adSlot->getLibraryAdSlot()->getId();
            if(!in_array($libAdSlotId, $libAdSlotIdArray)) {
                $libAdSlotIdArray[]= $libAdSlotId;
                $filteredAdSlots[] = $adSlot;
            }
        }

        $request->request->set('adSlot', $filteredAdSlots);

        return $this->post($request);
    }

    /**
     * @param Request $request
     * @return array|View|null|FormTypeInterface
     * @throws \Exception
     */
    protected function post(Request $request)
    {
        try {
            $newEntities = $this->getHandler()->post(
                $request->request->all()
            );

            if ( null == $newEntities) {
                throw new \Exception ('There is a error when creating ad tags');
            }

            $routeOptions = array(
                '_format' => $request->get('_format')
            );

            if (count($newEntities) == 1) {
                return $this->addRedirectToResource($newEntities[0], Codes::HTTP_CREATED, $routeOptions);
            }

            // if we create multiple entities then we cannot add the redirect
            return $this->view($data = null, Codes::HTTP_CREATED, $routeOptions);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }


    /**
     * Update an existing adTag from the submitted data or create a new adTag
     *
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when the resource is created",
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function putAction(Request $request, $id)
    {
        return $this->put($request, $id);
    }

    /**
     * Update estCpm for ad tag.
     *
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="estCpm", description="Cpm rate of adTag")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the cpm in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want setting in a range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param $id
     *
     * @return View
     */
    public function putEstcpmAction($id)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        //check param estCpm is number?
        $estCpmParam = $paramFetcher->get('estCpm');
        if (!is_numeric($estCpmParam)) {
            throw new InvalidArgumentException('estCpm should be numeric');
        }

        $estCpm = (float)$estCpmParam;
        if (!is_numeric($estCpm) || $estCpm < 0) {
            throw new InvalidArgumentException('estCpm should be numeric and positive value');
        }

        $adTag = $this->get('tagcade.domain_manager.ad_tag')->find($id);
        if (!$adTag) {
            throw new NotFoundHttpException('That adTag does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adTag)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'), true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'), true);

        $this->get('tagcade.worker.manager')->updateRevenueForAdTag($adTag, $estCpm, $startDate, $endDate);

        // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
        $event = new HandlerEventLog('PUT', $adTag);
        $event->addChangedFields('estCpm', '', $estCpm, $startDate, $endDate);
        $this->getHandler()->dispatchEvent($event);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Update an existing adTag from the submitted data or create a new adTag at a specific location
     *
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchAction(Request $request, $id)
    {
        /** @var AdTagInterface $adTag */
        $adTag = $this->one($id);

        if (array_key_exists('adSlot', $request->request->all())) {
            $adSlot = (int)$request->get('adSlot');
            if ($adTag->getAdSlotId() != $adSlot) {
                throw new InvalidArgumentException('adSlot in invalid');
            }
        }

//        if(array_key_exists('libraryAdTag', $request->request->all())) {
//            $libraryAdTag = $request->get('libraryAdTag');
//            if(is_array($libraryAdTag) && array_key_exists('adNetwork', $libraryAdTag)) {
//                $adNetwork = (int)$libraryAdTag['adNetwork'];
//                if($adTag->getAdNetworkId() != $adNetwork) {
//                    throw new InvalidArgumentException('adNetwork in invalid');
//                }
//            }
//        }

        return $this->patch($request, $id);
    }

    /**
     * Unlink an existing adTag from the library tag
     *
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchUnlinkAction(Request $request, $id)
    {
        /** @var AdTagInterface $adTag */
        $adTag = $this->one($id);

        if (array_key_exists('adSlot', $request->request->all())) {
            $adSlot = (int)$request->get('adSlot');
            if ($adTag->getAdSlotId() != $adSlot) {
                throw new InvalidArgumentException('adSlot in invalid');
            }
        }

        /** @var UnlinkServiceInterface $unlinkService */
        $unlinkService = $this->get('tagcade_api.service.tag_library.unlink_service');

        $unlinkService->unlinkForAdTag($adTag);

        return $this->view('', Codes::HTTP_NO_CONTENT);
    }

    /**
     * Delete an existing adTag
     *
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return View
     *
     * @throws NotFoundHttpException when the resource not exist
     */
    public function deleteAction($id)
    {
        return $this->delete($id);
    }

    protected function getResourceName()
    {
        return 'adtag';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_adtag';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ad_tag');
    }
}
