<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;

/**
 * @Rest\RouteResource("LibraryExpression")
 */
class LibraryExpressionController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all library expression
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section="Library expressions",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return LibraryExpressionInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        /** @var LibraryExpressionRepositoryInterface $libraryExpressionRepository */
        $libraryExpressionRepository = $this->get('tagcade.repository.library_expression');
        if ($request->query->count() < 1) {
            if ($role instanceof PublisherInterface) {
                return $libraryExpressionRepository->getLibraryExpressionsForPublisher($role);
            }

            return $this->all();
        }

        $qb = $libraryExpressionRepository->getLibraryExpressionsForUserWithPagination($role, $this->getParams());
        return $this->getPagination($qb, $request);
    }

    /**
     *
     *
     * Get a single library expression for the given id
     *
     * @ApiDoc(
     *  section="Library expressions",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return LibraryExpressionInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a library expression from the submitted data
     *
     * @ApiDoc(
     *  section="Library expressions",
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
     * Update an existing library expression from the submitted data or create a new adTag library
     *
     * @ApiDoc(
     *  section="Library expressions",
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
     * Update an existing library expression from the submitted data or create a new adTag library at a specific location
     *
     * @ApiDoc(
     *  section="Library expressions",
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
     * Delete an existing library expression
     *
     * @ApiDoc(
     *  section="Library expressions",
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
        return 'libraryexpression';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_libraryexpression';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.library_expression');
    }
}
