<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\SegmentInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("Segment")
 */
class SegmentController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all segment
     *
     * @Rest\View(
     *      serializerGroups={"segment.detail", "user.summary"}
     * )
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return SegmentInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single segment for the given id
     *
     * @Rest\View(
     *      serializerGroups={"segment.detail", "user.summary"}
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
     * @return SegmentInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }


    /**
     * Delete an existing segment
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
        return 'segment';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_segment';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.segment');
    }
}
