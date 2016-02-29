<?php

namespace Tagcade\Bundle\UserSystem\PublisherBundle\Entity;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Bundle\UserBundle\Entity\User as BaseUser;
use Tagcade\Model\User\UserEntityInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Tagcade\Exception\LogicException;

class User extends BaseUser implements PublisherInterface
{

    protected $id;
    protected $uuid;
    protected $billingRate;

    protected $firstName;
    protected $lastName;
    protected $company;
    protected $phone;
    protected $city;
    protected $state;
    protected $address;
    protected $postalCode;
    protected $country;
    protected $settings; //json string represent setting for report bundle
    protected $tagDomain;
    /** @var array|string[] */
    protected $exchanges;

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return self
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return self
     */
    public function generateAndAssignUuid()
    {
        if ($this->uuid === null || empty($this->uuid)) {
            try {
                $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $this->getEmail());
                $this->uuid = $uuid5->toString();

            } catch(UnsatisfiedDependencyException $e) {
                throw new LogicException($e->getMessage());
            }
        }

        return $this;
    }


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

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param mixed $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return string
     */
    public function getTagDomain()
    {
        return $this->tagDomain;
    }

    /**
     * @param string $tagDomain
     * @return self
     *
     */
    public function setTagDomain($tagDomain)
    {
        $this->tagDomain = $tagDomain;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExchanges()
    {
        if (!is_array($this->exchanges)) {
            $this->exchanges = [];
        }

        return $this->exchanges;
    }

    /**
     * @inheritdoc
     */
    public function setExchanges($exchanges)
    {
        $this->exchanges = is_array($exchanges) ? $exchanges : (null === $exchanges ? [] : [$exchanges]);
        return $this;
    }
}
