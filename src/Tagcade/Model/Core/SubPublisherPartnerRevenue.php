<?php

namespace Tagcade\Model\Core;


use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherPartnerRevenue implements SubPublisherPartnerRevenueInterface
{
    const REVENUE_OPTION_NONE = 0;
    const REVENUE_OPTION_CPM_FIXED = 1;
    const REVENUE_OPTION_CPM_PERCENT = 2;

    private static $supportedRevenueOptions = [
        self::REVENUE_OPTION_NONE,
        self::REVENUE_OPTION_CPM_FIXED,
        self::REVENUE_OPTION_CPM_PERCENT
    ];

    protected $id;
    /** @var SubPublisherInterface */
    protected $subPublisher;
    /** @var int */
    protected $revenueOption;
    /** @var float, scale 4 */
    protected $revenueValue;

    /**
     * @var AdNetworkPartner
     */
    protected $adNetworkPartner;

    public function __construct()
    {
        $this->revenueValue = self::REVENUE_OPTION_NONE;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @inheritdoc
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher)
    {
        $this->subPublisher = $subPublisher;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubPublisherId()
    {
        return $this->subPublisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function getRevenueOption()
    {
        return $this->revenueOption;
    }

    /**
     * @inheritdoc
     */
    public function setRevenueOption($revenueOption)
    {
        if ($this->isSupportedRevenueOption($revenueOption)) {
            $this->revenueOption = $revenueOption;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRevenueValue()
    {
        return $this->revenueValue;
    }

    /**
     * @inheritdoc
     */
    public function setRevenueValue($revenueValue)
    {
        $revenueValue = filter_var($revenueValue, FILTER_VALIDATE_FLOAT);
        $this->revenueValue = false == $revenueValue ? 0 : round((float)$revenueValue, 4);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworkPartner()
    {
        return $this->adNetworkPartner;
    }

    /**
     * @inheritdoc
     */
    public function setAdNetworkPartner(AdNetworkPartner $adNetworkPartner)
    {
        $this->adNetworkPartner = $adNetworkPartner;

        return $this;
    }

    /**
     * check if is supported Revenue Option
     *
     * @param int|mixed $revenueOption
     * @return bool
     */
    public static function isSupportedRevenueOption($revenueOption)
    {
        return in_array($revenueOption, self::$supportedRevenueOptions);
    }

    /**
     * check if is supported Revenue Option
     *
     * @param int|mixed $revenueOption
     * @param mixed $revenueValue
     * @return bool
     */
    public static function isSupportedRevenueOptionAndValue($revenueOption, $revenueValue)
    {
        if (!self::isSupportedRevenueOption($revenueOption)) {
            return false;
        }

        switch ($revenueOption) {
            case self::REVENUE_OPTION_NONE:
                break;

            case self::REVENUE_OPTION_CPM_FIXED:
                $revenueValue = filter_var($revenueValue, FILTER_VALIDATE_FLOAT);

                if (false == $revenueValue || $revenueValue < 0) {
                    return false;
                }

                break;

            case self::REVENUE_OPTION_CPM_PERCENT:
                $revenueValue = filter_var($revenueValue, FILTER_VALIDATE_FLOAT);

                if (false == $revenueValue || $revenueValue <= 0 || $revenueValue >= 100) {
                    return false;
                }

                break;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->subPublisher->getId() . '-' . $this->adNetworkPartner->getId();
    }
}