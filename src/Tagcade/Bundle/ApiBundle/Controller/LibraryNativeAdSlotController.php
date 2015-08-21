<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("LibraryNativeAdSlot")
 */
class LibraryNativeAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * @Rest\View(
     *      serializerGroups={"librarynativeadslot.summary", "slotlib.extra", "user.summary", "nativeadslot.summary", "site.summary"}
     * )
     *
     * Get all native ad slots
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return LibraryNativeAdSlotInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     *
     * Get a single native ad slots for the given id
     *
     * @Rest\View(
     *      serializerGroups={"librarynativeadslot.detail", "slotlib.summary", "user.summary", "nativeadslot.summary", "site.summary"}
     * )
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return LibraryNativeAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * add an new adtag library to the current native ad slots
     *
     * @Rest\POST("/librarynativeadslots/{id}/adtag", requirements={"id" = "\d+"})
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postAdtagAction(Request $request, $id)
    {
        /** @var LibraryNativeAdSlotInterface $libraryNativeAdSlot */
        $libraryNativeAdSlot = $this->getOr404($id);

        $request->request->set('libraryAdSlot', $id);

        $request->request->set('refId', uniqid("", true));
        // move the creating AdTag to library
        $libraryAdTag = $request->request->get('libraryAdTag');
        
        if(is_array($libraryAdTag)){
            $libraryAdTag['visible'] = true;
            $request->request->set('libraryAdTag', $libraryAdTag);
        }

        $this->get('tagcade_api.handler.library_slot_tag')->post($request->request->all());

        return $this->view(null, Codes::HTTP_CREATED);
    }


    /**
     * Create a native ad slots from the submitted data
     *
     * @ApiDoc(
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
        if(!array_key_exists('visible', $request->request->all()))
        {
            $request->request->add(array('visible' => true));
        }

        return $this->post($request);
    }

    /**
     * Update an existing native ad slots from the submitted data or create a new one at a specific location
     *
     * @ApiDoc(
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
        return $this->patch($request, $id);
    }


    /**
     * Get those AdSlots which refer to the current AdSlot Library
     * @Rest\View(
     *      serializerGroups={"adslot.summary" , "slotlib.summary", "user.summary", "nativeadslot.summary", "librarynativeadslot.summary", "site.summary"}
     * )
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return NativeAdSlotInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAssociatedadslotsAction($id){
        /** @var LibraryNativeAdSlotInterface $entity */
        $entity = $this->one($id);

        return $entity->getAdSlots();
    }


    /**
     * Get those AdTags which belong to the given AdSlot Library, also have been moved to AdTag Library
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return AdTagInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAdtagsAction($id){
        /** @var LibraryNativeAdSlotInterface $entity */
        $entity = $this->one($id);
        return $this->get('tagcade.repository.library_slot_tag')->getByLibraryAdSlot($entity);
    }

    /**
     * Delete an existing adTag library
     *
     * @ApiDoc(
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
        /** @var LibraryNativeAdSlotInterface $libraryNativeAdSlot */
        $libraryNativeAdSlot = $this->getOr404($id);

        $referencingSlots = $libraryNativeAdSlot->getAdSlots()->toArray();
        if (count($referencingSlots) > 0) {
            throw new BadRequestHttpException('There are some slots still referring to this library');
        }

        return $this->delete($id);
    }


    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'librarynativeadslot';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_librarynativeadslot';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.library_native_ad_slot');
    }
}