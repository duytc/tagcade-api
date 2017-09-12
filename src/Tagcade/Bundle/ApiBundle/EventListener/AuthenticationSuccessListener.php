<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
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

    /** @var JWTEncoderInterface */
    protected $jwtEncoder;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(JWTResponseTransformer $jwtResponseTransformer, UserManagerInterface $userManager, JWTManagerInterface $jwtManager, JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtResponseTransformer = $jwtResponseTransformer;
        $this->userManager = $userManager;
        $this->jwtManager = $jwtManager;
        $this->jwtEncoder = $jwtEncoder;
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
            if (!$masterAccount->getUser()->isEnabled()) {
                throw new AuthenticationException();
            }

            $tokenString = $this->jwtManager->create($masterAccount);

            // add flag for 2nd login, use for patch action
            $payload = $this->jwtEncoder->decode($tokenString);
            $payload[PublisherInterface::IS_2ND_LOGIN] = true;
            $tokenString = $this->jwtEncoder->encode($payload);

            $data = $this->jwtResponseTransformer->transform(['token' => $tokenString], $masterAccount);

            // add flag for 2nd login return to UI
            $data[PublisherInterface::IS_2ND_LOGIN] = true;

            $event->setData($data);
        } else {
            // normal login
            $data = $this->jwtResponseTransformer->transform($data, $user);

            // add flag for 2nd login...
            $data[PublisherInterface::IS_2ND_LOGIN] = false;

            $event->setData($data);
        }
    }
}