<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\BillingConfiguration as BillingConfigurationModel;

class BillingConfiguration extends BillingConfigurationModel
{
    protected $id;
    protected $publisher;
    protected $module;
    protected $billingFactor;
    protected $tiers;
    protected $defaultConfig;
}