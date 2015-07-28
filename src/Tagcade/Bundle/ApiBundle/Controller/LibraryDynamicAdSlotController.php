<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Handler\Handlers\Core\LibraryDynamicAdSlotHandlerAbstract;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("LibraryDynamicAdSlot")
 */
class LibraryDynamicAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all ad slots
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single adSlot for the given id
     * @Rest\View(serializerEnableMaxDepthChecks=true)
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
     * @return LibraryDynamicAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }


    /**
     * Create a adSlot library from the submitted data
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
     * Update an existing adSlot from the submitted data or create a new adSlot at a specific location
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
        $params = $request->request->all();

        if (array_key_exists('visible', $params) && false == $params['visible']) {
            /**
             * @var LibraryDynamicAdSlotInterface $libraryDynamicAdSlot;
             */
            $libraryDynamicAdSlot = $this->getOr404($id);
            $referencingSlots = $libraryDynamicAdSlot->getDynamicAdSlots()->toArray();
            if (count($referencingSlots) > 0) {
                throw new BadRequestHttpException('There are some slots still referencing to this library');
            }

        }

        return $this->patch($request, $id);
    }


    /**
     * Get those AdSlots which refer to the current AdSlot Library
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
     * @return DynamicAdSlotInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAssociatedadslotsAction($id){
        /** @var LibraryDynamicAdSlotInterface $entity */
        $entity = $this->one($id);

        return $entity->getDynamicAdSlots();
    }

    /**
     * Delete an existing adSlot
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
        return $this->delete($id);
    }


    protected function getResourceName()
    {
        return 'librarydynamicadslot';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_librarydynamicadslot';
    }

    /**
     * @return LibraryDynamicAdSlotHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->get('tagcade_api.handler.library_dynamic_ad_slot');
    }

}