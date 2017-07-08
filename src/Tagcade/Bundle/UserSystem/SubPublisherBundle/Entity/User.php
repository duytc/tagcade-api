<?php

namespace Tagcade\Bundle\UserSystem\SubPublisherBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tagcade\Bundle\UserBundle\Entity\User as BaseUser;
use Tagcade\Entity\Core\BillingConfiguration;
use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Core\SubPublisherPartnerRevenueInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class User extends BaseUser implements SubPublisherInterface, PublisherInterface
{
    protected $id;

    /** @var PublisherInterface */
    protected $publisher;

    /** @var array */
    protected $enabledModules;

    /** @var bool */
    protected $demandSourceTransparency;

    /** @var bool enable view tab tagcade report, also tab comparison report, in unified report */
    protected $enableViewTagcadeReport;

    /** @var SegmentInterface[] */
    protected $segments;

    /** @var array|SubPublisherPartnerRevenueInterface[] */
    protected $subPublisherPartnerRevenue;

    /** @var array|SiteInterface[] */
    protected $sites;

    /** @var PublisherInterface */
    protected $emailSendAlert;
    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    public function __construct()
    {
        parent::__construct();

        $this->demandSourceTransparency = false;
        $this->enableViewTagcadeReport = false;
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return \Tagcade\Model\Core\SegmentInterface[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param \Tagcade\Model\Core\SegmentInterface[] $segments
     * @return self
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEnabledModules()
    {
        return $this->publisher->getEnabledModules();
    }

    /**
     * @inheritdoc
     */
    public function isDemandSourceTransparency()
    {
        return $this->demandSourceTransparency;
    }

    /**
     * @inheritdoc
     */
    public function setDemandSourceTransparency($demandSourceTransparency)
    {
        $this->demandSourceTransparency = $demandSourceTransparency;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isEnableViewTagcadeReport()
    {
        return $this->enableViewTagcadeReport;
    }

    /**
     * @inheritdoc
     */
    public function setEnableViewTagcadeReport($enableViewTagcadeReport)
    {
        $this->enableViewTagcadeReport = $enableViewTagcadeReport;

        return $this;
    }

    /**
     * @return UserEntityInterface
     */
    public function getUser()
    {
        // TODO remove this method
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUuid()
    {
        throw new NotSupportedException('getUuid Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setUuid($uuid)
    {
        throw new NotSupportedException('setUuid Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function generateAndAssignUuid()
    {
        throw new NotSupportedException('generateAndAssignUuid Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getBillingRate()
    {
        throw new NotSupportedException('getBillingRate Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setBillingRate($billingRate)
    {
        throw new NotSupportedException('setBillingRate Not supported by SubPublisher');
    }

    public function getFirstName()
    {
        throw new NotSupportedException('getFirstName Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setFirstName($firstName)
    {
        throw new NotSupportedException('setFirstName Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getLastName()
    {
        throw new NotSupportedException('getLastName Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setLastName($lastName)
    {
        throw new NotSupportedException('setLastName Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getCompany()
    {
        throw new NotSupportedException('getCompany Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setCompany($company)
    {
        throw new NotSupportedException('setCompany Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getPhone()
    {
        throw new NotSupportedException('getPhone Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setPhone($phone)
    {
        throw new NotSupportedException('setPhone Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        throw new NotSupportedException('getCity Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setCity($city)
    {
        throw new NotSupportedException('setCity Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        throw new NotSupportedException('getState Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setState($state)
    {
        throw new NotSupportedException('setState Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        throw new NotSupportedException('getAddress Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setAddress($address)
    {
        throw new NotSupportedException('setAddress Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getPostalCode()
    {
        throw new NotSupportedException('getPostalCode Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setPostalCode($postalCode)
    {
        throw new NotSupportedException('setPostalCode Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        throw new NotSupportedException('getCountry Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setCountry($country)
    {
        throw new NotSupportedException('setCountry Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getSettings()
    {
        throw new NotSupportedException('getSettings Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setSettings($settings)
    {
        throw new NotSupportedException('setSettings Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getTagDomain()
    {
        throw new NotSupportedException('getTagDomain Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     *
     */
    public function setTagDomain($tagDomain)
    {
        throw new NotSupportedException('setTagDomain Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getExchanges()
    {
        throw new NotSupportedException('getExchanges Not supported by SubPublisher');
    }

    public function setExchanges($exchanges)
    {
        throw new NotSupportedException('setExchanges Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function getBidders()
    {
        throw new NotSupportedException('getBidders Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setBidders($bidders)
    {
        throw new NotSupportedException('getBidders Not supported by SubPublisher');

    }

    /**
     * @inheritdoc
     */
    public function getSubPublisherPartnerRevenue()
    {
        return $this->subPublisherPartnerRevenue;
    }

    /**
     * @inheritdoc
     */
    public function setSubPublisherPartnerRevenue($subPublisherPartnerRevenue)
    {
        $this->subPublisherPartnerRevenue = $subPublisherPartnerRevenue;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSites()
    {
        $this->sites = $this->sites instanceof Collection ? $this->sites->toArray() : $this->sites;

        return $this->sites;
    }

    /**
     * @inheritdoc
     */
    public function setSites(array $sites)
    {
        $this->sites = $sites;

        return $this;
    }

    public function isTestAccount()
    {
        throw new NotSupportedException('this property is currently not supported');
    }

    public function setTestAccount($testAccount)
    {
        throw new NotSupportedException('this property is currently not supported');
    }

    /**
     * @inheritdoc
     */
    public function getBillingConfigs()
    {
        throw new NotSupportedException('this property is currently not supported');
    }

    /**
     * @inheritdoc
     */
    public function setBillingConfigs($billingConfigs)
    {
        throw new NotSupportedException('this property is currently not supported');
    }

    /**
     * @inheritdoc
     */
    public function addBillingConfig(BillingConfiguration $billingConfiguration)
    {
        throw new NotSupportedException('this property is currently not supported');
    }

    public function getSubPublishers()
    {
        throw new NotSupportedException('this property is currently not supported');
    }

    public function setSubPublishers($subPublishers)
    {
        throw new NotSupportedException('this property is currently not supported');
    }


    /**
     * Returns the user roles
     * -- Override --
     * NOTE: merge all roles of current SubPublisher and roles of own Publisher!!!
     *
     * @return array The roles
     */
    public function getRoles()
    {
        // get roles of own Publisher, note: WITHOUT role 'ROLE_PUBLISHER'
        $roles = $this->publisher->getRoles();

        if (($key = array_search('ROLE_PUBLISHER', $roles)) !== false) {
            unset($roles[$key]);
        }

        // merge all roles of this and own Publisher
        $roles = array_merge($this->roles, $roles);

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * @inheritdoc
     */
    public function getMasterAccount()
    {
        throw new NotSupportedException('getMasterAccount Not supported by SubPublisher');
    }

    /**
     * @inheritdoc
     */
    public function setMasterAccount(PublisherInterface $masterAccount = null)
    {
        throw new NotSupportedException('setMasterAccount Not supported by SubPublisher');
    }

    /**
     * @return mixed
     */
    public function getEmailSendAlert()
    {
        // TODO: Implement getEmailSendAlert() method.
        return $this->emailSendAlert;
    }

    /**
     * @param $emailSendAlert
     * @return
     */
    public function setEmailSendAlert($emailSendAlert)
    {
        // TODO: Implement setEmailSendAlert() method.
        $this->emailSendAlert = $emailSendAlert;
    }
}
