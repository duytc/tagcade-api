<?php

namespace Tagcade\Bundle\AdminApiBundle\EventListener;

use Tagcade\Bundle\AdminApiBundle\Entity\ActionLog;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Tagcade\Bundle\UserBundle\Event\LogEventInterface;
use Tagcade\Model\User\UserEntityInterface;

class ActionLogEventListener
{
    /**
     * @var UserEntityInterface
     */
    protected $user;

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param UserEntityInterface $user
     * @param ObjectManager $em
     * @param RequestStack $requestStack
     */
    public function __construct(UserEntityInterface $user, ObjectManager $em, RequestStack $requestStack)
    {
        $this->user = $user;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @param LogEventInterface $event
     */
    public function onHandlerEvent(LogEventInterface $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $actionLog = (new ActionLog())
            ->setUser($this->user)
            ->setIp($request->getClientIp())
            ->setAction($event->getAction())
            ->setData($event->getData());
        ;

        $this->em->persist($actionLog);
        $this->em->flush();
    }

}