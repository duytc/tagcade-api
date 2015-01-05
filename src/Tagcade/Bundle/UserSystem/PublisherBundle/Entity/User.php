<?php

namespace Tagcade\Bundle\UserSystem\PublisherBundle\Entity;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Bundle\UserBundle\Entity\User as BaseUser;
use Tagcade\Model\User\UserEntityInterface;

class User extends BaseUser implements PublisherInterface
{
    protected $id;

    protected $billingRate;

    /**
     * @inheritdoc
     */
    public function getBillingRate()
    {
        return $this->billingRate;
    }

    /**
     * @inheritdoc
     */
    public function setBillingRate($billingRate)
    {
        $this->billingRate = $billingRate;
    }

    /**
     * @return UserEntityInterface
     */
    public function getUser()
    {
        // TODO remove this method
        return $this;
    }


}
