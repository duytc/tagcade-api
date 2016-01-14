<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * @Rest\RouteResource("LibraryAdtag")
 */
class LibraryAdTagController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all library ad tags
     *
     * @Rest\View(serializerGroups={"libraryadtag.summary", "adnetwork.summary", "user.summary", "adtag.summary"})
     * Get all adtag library
     *
     * @ApiDoc(
     *  section="Library ad tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return LibraryAdTagInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get single library ad tag
     *
     * @Rest\View(
     *      serializerGroups={"libraryadtag.detail", "adnetwork.summary", "user.summary", "adtag.summary"}
     * )
     *
     * Get a single adTag library for the given id
     *
     * @ApiDoc(
     *  section="Library ad tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return LibraryAdTagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a adTag library from the submitted data
     *
     * @ApiDoc(
     *  section="Library ad tags",
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
     * Update an existing adTag library from the submitted data or create a new adTag library
     *
     * @ApiDoc(
     *  section="Library ad tags",
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
     * Update an existing adTag library from the submitted data or create a new adTag library at a specific location
     *
     * @ApiDoc(
     *  section="Library ad tags",
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
             * @var LibraryAdTagInterface $libraryAdTag;
             */
            $libraryAdTag = $this->getOr404($id);
            $referencingTags = $libraryAdTag->getAdTags()->toArray();
            if (count($referencingTags) > 0) {
                throw new BadRequestHttpException('There are some tags still referencing this library');
            }

        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing adTag library
     *
     * @ApiDoc(
     *  section="Library ad tags",
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

    /**
     * Get ad tags linked to this library ad tag
     *
     * @ApiDoc(
     *  section="Library ad tags",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"adtag.detail", "adslot.detail", "nativeadslot.summary", "displayadslot.summary", "dynamicadslot.summary", "libraryadtag.summary", "slotlib.summary", "site.summary"})
     * @param $id
     * @return array
     */
    public function getAssociatedadtagsAction($id){
        /**
         * @var LibraryAdTagInterface $libraryAdTag
         */
        $libraryAdTag = $this->one($id);

        return $libraryAdTag->getAdTags()->toArray();
    }

    protected function getResourceName()
    {
        return 'libraryadtag';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_libraryadtag';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.library_ad_tag');
    }
}
