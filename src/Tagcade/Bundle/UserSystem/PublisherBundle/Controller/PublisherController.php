<?php

namespace Tagcade\Bundle\UserSystem\PublisherBundle\Controller;

use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * @Rest\RouteResource("publishers/current")
 */
class PublisherController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get current publisher
     *
     * @return \Tagcade\Bundle\UserBundle\Entity\User
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction()
    {
        $publisherId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->one($publisherId);
    }

    /**
     * Get the javascript display ad tags for all ron ad slot of this publisher
     *
     * @return array
     */
    public function getRonjstagsAction()
    {
        $publisherId = $this->get('security.context')->getToken()->getUser()->getId();

        /** @var PublisherInterface $publisher */
        $publisher = $this->one($publisherId);

        return $this->get('tagcade.service.tag_generator')
            ->getRonTagsForPublisher($publisher);
    }

    /**
     * @return array
     */
    public function getJspassbackAction()
    {
        $publisherId = $this->get('security.context')->getToken()->getUser()->getId();
        $publisher = $this->one($publisherId);
        return $this->get('tagcade.service.tag_generator')->getTagsForPassback($publisher);
    }


    /**
     * Update current publisher from the submitted data
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchAction(Request $request)
    {
        $publisherId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->patch($request, $publisherId);
    }

    /**
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'publisher_api_1_get_current';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_admin_api.handler.user');
    }
}
