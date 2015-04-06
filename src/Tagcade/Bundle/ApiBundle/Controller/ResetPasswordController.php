<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
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
class ResetPasswordController extends FOSRestController
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
        $this->get('tagcade_api.mailer.mailer')->sendResettingEmailMessage($user);

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
        //get user manager as publisher
        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');

        //find user by token
        $publisher = $publisherManager->findUserByConfirmationToken($token);
        if (null === $publisher) {
            return $this->view(null, Codes::HTTP_NOT_FOUND);
        }

        //check if token expired
        $ttl = $this->container->getParameter('tagcade_user_system_publisher.resetting.token_ttl');
        if (!$publisher->isPasswordRequestNonExpired($ttl)) {
            return $this->view(null, Codes::HTTP_REQUEST_TIMEOUT);
        }

        //using an event FOSUserEvents::SECURITY_IMPLICIT_LOGIN for RollerWorks auto setting user system as user_system_publisher (userDiscriminator->setCurrentUser(...))
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(FOSUserEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($publisher, $request));

        //create form factory, set user discriminator as tagcade_user_system_publisher
        $formFactory = $this->get('rollerworks_multi_user.resetting.form.factory');

        //create form
        /** @var FormInterface $form */
        $form = $formFactory->createForm();
        $form->setData($publisher);

        //form handling request
        $form->handleRequest($request);

        //validate form and then update to db for publisher
        if ($form->isValid()) {
            $publisherManager->updateCanonicalFields($publisher);
            $publisher->setConfirmationToken(null);
            $publisher->setPasswordRequestedAt(null);
            $publisher->setEnabled(true);
            $publisherManager->updateUser($publisher);

            return $this->view(null, Codes::HTTP_ACCEPTED);
        }

        return $this->view($form->getErrors(), Codes::HTTP_BAD_REQUEST);
    }
}
