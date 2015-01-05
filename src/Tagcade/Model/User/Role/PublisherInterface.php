<?php

namespace Tagcade\Model\User\Role;

interface PublisherInterface extends UserRoleInterface
{
    /**
     * @return float
     */
    public function getBillingRate();

    /**
     * @param float $billingRate
     */
    public function setBillingRate($billingRate);
}