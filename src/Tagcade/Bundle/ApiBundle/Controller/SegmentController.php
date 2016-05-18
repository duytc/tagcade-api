<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\User\Role\AdminInterface;

/**
 * @Rest\RouteResource("Segment")
 */
class SegmentController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all segments
     *
     * @Rest\View(
     *      serializerGroups={"segment.detail", "user.summary"}
     * )
     *
     * @Rest\QueryParam(name="publisher", requirements={"\d+"}, nullable=true)*
     * @Rest\QueryParam(name="type", requirements="(custom|publisher)", nullable=true)*
     *
     * @ApiDoc(
     *  section="Segments",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     * @param Request $request the resource request
     * @return SegmentInterface[]
     */
    public function cgetAction(Request $request)
    {
        $typeSegment = $request->get('type', null);

        if (null === $typeSegment) {
            return $this->all();
        }

        $publisherId = $request->get('publisher', null);
        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');

        $publisher = $this->getUser() instanceof AdminInterface ? $publisherManager->find($publisherId) : $this->getUser();

        return $this->get('tagcade.repository.segment')->getSegmentsByTypeForPublisher($publisher, $typeSegment);
    }

    /**
     * Get a single segment for the given id
     *
     * @Rest\View(
     *      serializerGroups={"segment.detail", "user.summary"}
     * )
     * @ApiDoc(
     *  section="Segments",
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
     *  section="Segments",
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

    /**
     * @return \Tagcade\Handler\Handlers\Core\Publisher\SegmentHandler
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.segment');
    }
}
