<?php

namespace Tagcade\Bundle\UserSystem\SubPublisherBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Bundle\UserBundle\Entity\User as BaseUser;
use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Core\SubPublisherSiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class User extends BaseUser implements SubPublisherInterface, PublisherInterface
{
    protected $id;

    /** @var PublisherInterface */
    protected $publisher;

    /** @var array|SubPublisherSiteInterface[] */
    protected $subPublisherSites;

    /** @var array */
    protected $enabledModules;

    /** @var SegmentInterface[] */
    protected $segments;

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
    public function getSubPublisherSites()
    {
        if (null === $this->subPublisherSites) {
            $this->subPublisherSites = new ArrayCollection();
        }

        return $this->subPublisherSites;
    }

    /**
     * @inheritdoc
     */
    public function setSubPublisherSites(array $subPublisherSites)
    {
        $this->subPublisherSites = $subPublisherSites;

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
}
