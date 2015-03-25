<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\UserSystem\PublisherBundle\TagcadeUserSystemPublisherEvents;

/**
 * Controller managing the resetting of the password.
 *
 * This support 2 service: sendEmailAction (for send resetting email) & ResetAction (for do resetting password)
 */
class ResettingController extends FOSRestController
{
    /**
     * Request reset user password: submit form and send email. This used code from RollerWorksUserBundle Controller
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');
        /** @var UserInterface $user */
        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');
        $user = $publisherManager->findUserByUsernameOrEmail($username);

        // check existed user
        if (null === $user) {
            return $this->view('invalid_username', Codes::HTTP_NOT_FOUND);
        }

        // check passwordRequest expired?
        $ttl = $this->container->getParameter('tagcade_user_system_publisher.resetting.token_ttl');

        if ($user->isPasswordRequestNonExpired($ttl)) {
            return $this->view('passwordAlreadyRequested', Codes::HTTP_ALREADY_REPORTED);
        }

        // generate confirmation Token
        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        // send resetting email
        $this->get('tagcade_user_system_publisher.resetting.mailer')->sendResettingEmailMessage($user);

        // update user
        $user->setPasswordRequestedAt(new \DateTime());

        $publisherManager->updateUser($user);

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Reset user password. This used code from FOSBundle Controller
     * @param Request $request
     * @param $token
     *
     * @return \FOS\RestBundle\View\View
     */
    public function resetAction(Request $request, $token)
    {
        //create form factory, set user discriminator as tagcade_user_system_publisher
        $formFactory = $this->get('rollerworks_multi_user.resetting.form.factory');
        $userDiscriminator = $this->container->get('rollerworks_multi_user.user_discriminator');
        $userDiscriminator->setCurrentUser('tagcade_user_system_publisher');
        $formFactory->setUserDiscriminator($userDiscriminator);

        //get user manager as publisher
        $userManager = $this->get('tagcade_user.domain_manager.publisher');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        //find user by token
        $user = $userManager->findUserByConfirmationToken($token);
        if (null === $user) {
            return $this->view(null, Codes::HTTP_NOT_FOUND);
        }

        /** @var FormInterface $form */
        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(TagcadeUserSystemPublisherEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            return $this->view(null, Codes::HTTP_ACCEPTED);
        }

        return $this->view(null, Codes::HTTP_BAD_REQUEST);
    }
}
