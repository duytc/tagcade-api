<?php

namespace Tagcade\Bundle\AdminApiBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Tagcade\Bundle\UserBundle\Event\LogEventInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\ModelInterface;

class HandlerEventLog extends Event implements LogEventInterface
{
    protected $action;
    protected $entity;

    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    const HTTP_PUT = 'PUT';
    const HTTP_PATCH = 'PATCH';
    const HTTP_DELETE = 'DELETE';

    const ADD = 'ADD';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';

    protected $allowedHttpMethods = [
        self::HTTP_POST,
        self::HTTP_PUT,
        self::HTTP_PATCH,
        self::HTTP_DELETE,
    ];

    /**
     * Maps http methods to user actions such as 'adding' or 'deleting'
     *
     * @var array
     */
    protected $actionMap = [
        self::HTTP_POST => self::ADD,
        self::HTTP_PUT => self::ADD,
        self::HTTP_PATCH => self::UPDATE,
        self::HTTP_DELETE => self::DELETE,
    ];

    public function __construct(ModelInterface $entity, $httpMethod)
    {
        $this->entity = $entity;

        if (!in_array($httpMethod, $this->allowedHttpMethods)) {
            throw new InvalidArgumentException('that method is not defined');
        }

        if (!array_key_exists($httpMethod, $this->actionMap)) {
            throw new InvalidArgumentException('that method is not supported');
        }

        $this->action = $this->actionMap[$httpMethod];
    }

    /**
     * @return ModelInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
       return $this->action;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
      return [
            'entity' => get_class($this->getEntity()),
            'id' => $this->getEntity()->getId()
        ];
    }
}
