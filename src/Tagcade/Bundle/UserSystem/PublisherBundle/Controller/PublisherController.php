<?php

namespace Tagcade\Bundle\UserSystem\PublisherBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Model\User\Role\SubPublisherInterface;

/**
 * @Rest\RouteResource("publishers/current")
 */
class PublisherController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get current publisher
     * @Rest\View(
     *      serializerGroups={"user.detail"}
     * )
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
     * @Rest\View(
     *      serializerGroups={"user.detail"}
     * )
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
     * Get token for sub publisher only
     * @Rest\Get("subpublishers/{subPublisherId}/token", requirements={"subPublisherId" = "\d+"})
     * @ApiDoc(
     *  section = "publisher",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     * @param $subPublisherId
     * @return array
     */
    public function getTokenAction($subPublisherId)
    {
        /**
         * @var SubPublisherManagerInterface $subPublisherManager;
         */
        $subPublisherManager = $this->get('tagcade_user.domain_manager.sub_publisher');
        $subPublisher = $subPublisherManager->find($subPublisherId);

        if (!$subPublisher instanceof SubPublisherInterface) {
            throw new NotFoundHttpException(sprintf('That sub publisher does not exist. The entered id is %s', $subPublisherId));
        }

        $currentUser = $this->getUser();

        if ((!$currentUser instanceof  AdminInterface) && ($subPublisher->getPublisher()->getId() != $currentUser->getId())) {
            throw new AccessDeniedException(sprintf('The user does not have right to access to sub publisher %s', $subPublisherId));
        }

        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
        $jwtTransformer = $this->get('tagcade_api.service.jwt_response_transformer');

        $tokenString = $jwtManager->create($subPublisher);

        return $jwtTransformer->transform(['token' => $tokenString], $subPublisher);
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
