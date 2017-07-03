<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Tagcade\Bundle\ApiBundle\Service\JWTResponseTransformer;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AuthenticationSuccessListener
{
    protected $jwtResponseTransformer;
    protected $userManager;
    /** @var JWTManagerInterface */
    protected $jwtManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(JWTResponseTransformer $jwtResponseTransformer, UserManagerInterface $userManager, JWTManagerInterface $jwtManager)
    {
        $this->jwtResponseTransformer = $jwtResponseTransformer;
        $this->userManager = $userManager;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $user->setLastLogin(new \DateTime());
        $this->userManager->updateUser($user);

        // if 2nd login => change 2nd login token to master account (publisher) token
        if ($user instanceof PublisherInterface && !($user instanceof SubPublisherInterface) && $user->getMasterAccount() instanceof PublisherInterface) {
            $masterAccount = $user->getMasterAccount();

            // reject 2nd-login if master account is disabled
            if (!$masterAccount->isEnabled()) {
                throw new AuthenticationException();
            }

            $tokenString = $this->jwtManager->create($masterAccount);
            $data = $this->jwtResponseTransformer->transform(['token' => $tokenString], $masterAccount);
            $event->setData($data);
        } else {
            // normal login
            $data = $this->jwtResponseTransformer->transform($data, $user);
            $event->setData($data);
        }
    }
}