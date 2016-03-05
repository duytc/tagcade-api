<?php

namespace Tagcade\Bundle\UserSystem\PublisherBundle\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

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

        /** @var PublisherInterface $publisher */
        $publisher = $this->getPublisher($publisherId);

        return $this->get('tagcade.service.tag_generator')->getTagsForPassback($publisher);
    }

    /**
     * get header of tag for publisher
     * @return array
     */
    public function getJsheadertagAction()
    {
        $publisherId = $this->get('security.context')->getToken()->getUser()->getId();

        /** @var PublisherInterface $publisher */
        $publisher = $this->one($publisherId);

        if (!$publisher->hasAnalyticsModule()) {
            throw new BadRequestHttpException('That publisher is not enabled Analytics module');
        }

        return $this->get('tagcade.service.tag_generator')->getHeaderForPublisher($publisher);
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
     * get account as Publisher or SubPublisher by publisherId
     * @param integer $publisherId
     * @return PublisherInterface Publisher or SubPublisher
     */
    protected function getPublisher($publisherId)
    {
        try {
            $publisher = $this->one($publisherId);
        } catch (\Exception $e) {
            $publisher = false;
        }

        if (!$publisher instanceof PublisherInterface) {
            // try again with SubPublisher
            $publisher = $this->get('tagcade_user_system_sub_publisher.user_manager')->findUserBy(array('id' => $publisherId));
        }

        if (!$publisher instanceof PublisherInterface) {
            throw new LogicException('The user should have the publisher role');
        }

        return $publisher;
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
