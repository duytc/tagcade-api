<?php

namespace Tagcade\Bundle\AdminApiBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Tagcade\Bundle\AdminApiBundle\Entity\ActionLog;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLogInterface;
use Tagcade\Bundle\AdminApiBundle\Event\UpdateSourceReportEmailConfigEventLog;
use Tagcade\Bundle\AdminApiBundle\Event\UpdateSourceReportSiteConfigEventLog;
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
        if ($event instanceof HandlerEventLog) {
            $this->onHandlerEventLog($event);
            return;
        }

        if ($event instanceof UpdateSourceReportEmailConfigEventLog) {
            $this->onUpdateSourceReportEmailConfig($event);
            return;
        }

        if ($event instanceof UpdateSourceReportSiteConfigEventLog) {
            $this->onUpdateSourceReportEmailConfig($event);
            return;
        }
    }

    /**
     * @param LogEventInterface $event
     */
    public function onHandlerEventLogin(LogEventInterface $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $actionLog = (new ActionLog())
            ->setUser($this->user)
            ->setIp($request->getClientIp())
            ->setServerIp($_SERVER['SERVER_ADDR'])
            ->setAction($event->getAction())
            ->setData($event->getData());;

        $this->em->persist($actionLog);
        $this->em->flush();
    }

    /**
     * @param HandlerEventLogInterface $event
     */
    public function onHandlerEventLog(HandlerEventLogInterface $event)
    {
        //get all $changedFields using as reason in data field of actionLog
        $oldEntity = $event->getOldEntity();
        $newEntity = $event->getNewEntity();

        //if using $newEntity for tracking changing => $data['changing'] updated manually, else $data = $event->getData() by default
        if (null !== $newEntity) {
            $changedFields = $this->getChangedFields($oldEntity, $newEntity);
            //if all fields changed => create or delete => not need tracking changing
            if(sizeof($changedFields) == sizeof($this->getFieldNames($oldEntity))){
                $changedFields = [];
            }
            $event->setChangedFields($changedFields);
        }

        $data = $event->getData();

        $request = $this->requestStack->getCurrentRequest();

        $actionLog = (new ActionLog())
            ->setUser($this->user)
            ->setIp($request->getClientIp())
            ->setServerIp($_SERVER['SERVER_ADDR'])
            ->setAction($event->getAction())
            ->setData($data);;

        $this->em->persist($actionLog);
        $this->em->flush();
    }

    /**
     * @param LogEventInterface $event
     */
    public function onUpdateSourceReportEmailConfig(LogEventInterface $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $actionLog = (new ActionLog())
            ->setUser($this->user)
            ->setIp($request->getClientIp())
            ->setServerIp($_SERVER['SERVER_ADDR'])
            ->setAction($event->getAction())
            ->setData($event->getData());;

        $this->em->persist($actionLog);
        $this->em->flush();
    }

    /**
     * @param LogEventInterface $event
     */
    public function onUpdateSourceReportSiteConfig(LogEventInterface $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $actionLog = (new ActionLog())
            ->setUser($this->user)
            ->setIp($request->getClientIp())
            ->setServerIp($_SERVER['SERVER_ADDR'])
            ->setAction($event->getAction())
            ->setData($event->getData());;

        $this->em->persist($actionLog);
        $this->em->flush();
    }

    /**
     * get all FieldNames of object
     *
     * @param object $entity
     * @return array
     */
    private function getFieldNames($entity)
    {
        return $this->em->getClassMetadata(get_class($entity))->getFieldNames();
    }

    /**
     * getChangedFields as:
     *
     * 'changedFields' => [
     *     [
     *         'name' => '',
     *         'oldVal' => '',
     *         'newVal' => '',
     *         'startDate' => '',
     *         'endDate' => ''
     *     ]
     * ]
     *
     * @param object $oldEntity , old entity need to be check changed fields
     * @param object $newEntity , new entity
     *
     * @return array array of difference fieldNames
     */
    private function getChangedFields($oldEntity, $newEntity)
    {
        //check same class
        if (get_class($oldEntity) !== get_class($newEntity)) {
            return null;
        }

        //get all fieldNames of object
        $fieldNames = $this->getFieldNames($oldEntity);
        if (null === $fieldNames || 1 > sizeof($fieldNames)) {
            return null;
        }

        //find out all changedFields
        $changedFields = [];

        foreach ($fieldNames as $fieldName) {
            $oldVal = $this->getFieldValueBName($oldEntity, $fieldName);
            $newVal = $this->getFieldValueBName($newEntity, $fieldName);

            if ($oldVal != $newVal) {
                //hide oldVal and newVal if password
                if ($oldEntity instanceof UserEntityInterface && 'password' === $fieldName) {
                    $oldVal = '***';
                    $newVal = '***';
                }

                $changedFields[] = [
                    'name' => $fieldName,
                    'oldVal' => $oldVal,
                    'newVal' => $newVal,
                    'startDate' => null,
                    'endDate' => null
                ];
            }

            unset($fieldName, $oldVal, $newVal);
        }

        return $changedFields;
    }

    /**
     * get Field Value of an Entity By Name
     *
     * @param object $entity , the entity
     * @param string $fieldName , the field name need getting value
     * @return mixed|null
     */
    private function getFieldValueBName($entity, $fieldName)
    {
        $value = null;

        $entityClass = ClassUtils::getRealClass(get_class($entity));
        try {
            $getterMethod = new \ReflectionMethod($entityClass, 'get' . ucfirst($fieldName));
            $value = $getterMethod->invoke($entity);
        } catch (\Exception $ex) {
            try {
                $getterMethod = new \ReflectionMethod($entityClass, 'is' . ucfirst($fieldName));
                $value = $getterMethod->invoke($entity);
            } catch (\Exception $ex) {
                try {
                    $getterMethod = new \ReflectionMethod($entityClass, 'has' . ucfirst($fieldName));
                    $value = $getterMethod->invoke($entity);
                } catch (\Exception $ex) {
                    $value = null;
                }
            }
        }

        return $value;
    }
}