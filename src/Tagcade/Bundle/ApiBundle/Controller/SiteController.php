<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

// annotations
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
// end annotations

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Tagcade\Exception\InvalidFormException;
use Tagcade\Model\SiteInterface;

use InvalidArgumentException;

class SiteController extends FOSRestController
{
    /**
     * List all sites.
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing sites.")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="How many sites to return.")
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getSitesAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('tagcade_api.handler.site')->all($limit, $offset);
    }

    /**
     * Get single Site.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Gets a Site for a given id",
     *  output = "Tagcade\Entity\Core\Site",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the site is not found"
     *  }
     * )
     *
     * @param int $id the site id
     *
     * @return array
     *
     * @throws NotFoundHttpException when site does not exist
     */
    public function getSiteAction($id)
    {
        $site = $this->getOr404($id);
        $this->checkUserPermission($site, 'view');

        return $site;
    }

    /**
     * Create a Site from the submitted data.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Creates a new site from the submitted data.",
     *  input = "Tagcade\Bundle\ApiBundle\Form\Type\SiteType",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the form has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postSiteAction(Request $request)
    {
        try {
            $newSite = $this->get('tagcade_api.handler.site')->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $newSite->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_site', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing site from the submitted data or create a new site at a specific location.
     *
     * @ApiDoc(
     *  resource = true,
     *  input = "Tagcade\Bundle\ApiBundle\Form\Type\SiteType",
     *  statusCodes = {
     *      201 = "Returned when the Site is created",
     *      204 = "Returned when successful",
     *      400 = "Returned when the form has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the site id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when site not exist
     */
    public function putSiteAction(Request $request, $id)
    {
        try {
            if (!($site = $this->container->get('tagcade_api.handler.site')->get($id))) {
                // create new
                $statusCode = Codes::HTTP_CREATED;
                $site = $this->container->get('tagcade_api.handler.site')->post(
                    $request->request->all()
                );
            } else {
                $this->checkUserPermission($site, 'edit');

                $statusCode = Codes::HTTP_NO_CONTENT;
                $site = $this->container->get('tagcade_api.handler.site')->put(
                    $site,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id' => $site->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_site', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing site from the submitted data or create a new site at a specific location.
     *
     * @ApiDoc(
     *  resource = true,
     *  input = "Tagcade\Bundle\ApiBundle\Form\Type\SiteType",
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the form has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the site id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when site not exist
     */
    public function patchSiteAction(Request $request, $id)
    {
        try {
            $site = $this->getOr404($id);
            $this->checkUserPermission($site, 'edit');

            $site = $this->container->get('tagcade_api.handler.site')->patch(
                $site,
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $site->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_site', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Fetch a Site or throw an 404 Exception.
     *
     * @param mixed $id
     * @return SiteInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($site = $this->container->get('tagcade_api.handler.site')->get($id))) {
            throw new NotFoundHttpException(sprintf("The site resource '%s' was not found or you do not have access", $id));
        }

        return $site;
    }

    /**
     * @param SiteInterface $site
     * @param string $permission
     * @return bool
     * @throws InvalidArgumentException if you pass an unknown permission
     * @throws AccessDeniedException
     */
    protected function checkUserPermission(SiteInterface $site, $permission = 'view')
    {
        if (!in_array($permission, ['view', 'edit'])) {
            throw new InvalidArgumentException('checking for an invalid permission');
        }

        if (false === $this->get('security.context')->isGranted($permission, $site)) {
            throw new AccessDeniedException(sprintf('You do not have permission to %s this site or it does not exist', $permission));
        }

        return true;
    }
}