<?php

namespace Tagcade\Bundle\AdminApiBundle\EventListener;

use Tagcade\Bundle\AdminApiBundle\Entity\ActionLog;
use Tagcade\Bundle\AdminApiBundle\Event\ActionLogEvent;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Tagcade\Model\User\UserEntityInterface;

class ActionLogEventListener
{
    protected $user;
    protected $em;
    protected $requestStack;

    // map action names to english words
    protected $actionMap = [
        ActionLogEvent::ADD => 'added',
        ActionLogEvent::UPDATE => 'edited',
        ActionLogEvent::DELETE => 'deleted',
    ];

    public function __construct(UserEntityInterface $user, ObjectManager $em, RequestStack $requestStack)
    {
        $this->user = $user;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function onActionLogEvent(ActionLogEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $actionLog = (new ActionLog())
            ->setUser($this->user)
            ->setIp($request->getClientIp())
            ->setAction($event->getAction())
            ->setData([
                'entity' => get_class($event->getEntity()),
                'id' => $event->getEntity()->getId()
            ])
        ;

        $this->em->persist($actionLog);
        $this->em->flush();
    }
}