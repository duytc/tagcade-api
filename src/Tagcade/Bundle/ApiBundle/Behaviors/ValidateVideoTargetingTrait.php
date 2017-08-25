<?php

namespace Tagcade\Bundle\ApiBundle\Behaviors;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoTargetingInterface;

trait ValidateVideoTargetingTrait
{
    public static $TARGETING_SUPPORTED_PLAYER_SIZES = [
        VideoTargetingInterface::TARGETING_PLAYER_SIZE_LARGE,
        VideoTargetingInterface::TARGETING_PLAYER_SIZE_MEDIUM,
        VideoTargetingInterface::TARGETING_PLAYER_SIZE_SMALL
    ];

    public static $TARGETING_SUPPORTED_REQUIRED_MACROS = [
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_IP_ADDRESS,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_USER_AGENT,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PAGE_URL,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_DOMAIN,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PAGE_TITLE,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PLAYER_WIDTH,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PLAYER_HEIGHT,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PLAYER_DIMENSIONS,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PLAYER_SIZE,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_DURATION,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_URL,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_ID,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_TITLE,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_DESCRIPTION,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_APP_NAME,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_USER_LAT,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_USER_LON,
        // new macros
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_COUNTRY,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_TIMESTAMP,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_WATERFALL_ID,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_DEMAND_TAG_ID,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_DEVICE_ID,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_DEVICE_NAME,
    ];

    public static $TARGETING_SUPPORTED_PLATFORMS = [
        VideoTargetingInterface::TARGETING_PLATFORM_FLASH,
        VideoTargetingInterface::TARGETING_PLATFORM_JS
    ];

    /**
     * validate supported Targeting keys
     *
     * @param array $targeting
     * @param array $targetingKeys
     * @return void if all keys passed
     * @throws InvalidArgumentException if one key not passed
     */
    private function validateTargetingKeys(array $targeting, array $targetingKeys)
    {
        foreach ($targeting as $targetingKey => $targetingValue) {
            if (!in_array($targetingKey, $targetingKeys)) {
                throw new InvalidArgumentException(sprintf('not supported targeting "%s"', $targetingKey));
            }
        }
    }

    /**
     * validate Targeting Player Size
     *
     * @param $targeting
     * @return void true if passed
     * @throws InvalidArgumentException if not passed
     */
    private function validateTargetingPlayerSize(array $targeting)
    {
        if (array_key_exists(VideoTargetingInterface::TARGETING_KEY_PLAYER_SIZE, $targeting)) {
            $targetingPlayerSizes = $targeting[VideoTargetingInterface::TARGETING_KEY_PLAYER_SIZE];

            if (!is_array($targetingPlayerSizes)
            ) {
                throw new InvalidArgumentException(sprintf('expect targeting "player_size" to be array'));
            }

            foreach ($targetingPlayerSizes as $targetingPlayerSize) {
                if (!$this->isSupportedTargetingPlayerSize($targetingPlayerSize)) {
                    throw new InvalidArgumentException(sprintf('not supported targeting player size "%s"', $targetingPlayerSize));
                }
            }
        }
    }

    /**
     * validate Targeting Required Macros
     *
     * @param $targeting
     * @return void true if passed
     * @throws InvalidArgumentException if not passed
     */
    private function validateTargetingRequiredMacros(array $targeting)
    {
        if (array_key_exists(VideoTargetingInterface::TARGETING_KEY_REQUIRED_MACROS, $targeting)) {
            $targetingRequiredMacros = $targeting[VideoTargetingInterface::TARGETING_KEY_REQUIRED_MACROS];

            if (!is_array($targetingRequiredMacros)
            ) {
                throw new InvalidArgumentException(sprintf('expect targeting "required_macros" to be array'));
            }

            foreach ($targetingRequiredMacros as $targetingRequiredMacro) {
                if (!$this->isSupportedTargetingRequiredMacro($targetingRequiredMacro)) {
                    throw new InvalidArgumentException(sprintf('not supported targeting required macro "%s"', $targetingRequiredMacro));
                }
            }
        }
    }

    /**
     * validate Targeting Platform
     *
     * @param $targeting
     * @return void true if passed
     * @throws InvalidArgumentException if not passed
     */
    private function validateTargetingPlatform(array $targeting)
    {
        if (array_key_exists(VideoTargetingInterface::TARGETING_KEY_PLATFORM, $targeting)) {
            $targetingPlatforms = $targeting[VideoTargetingInterface::TARGETING_KEY_PLATFORM];

            if (!is_array($targetingPlatforms)
            ) {
                throw new InvalidArgumentException(sprintf('expect targeting "platform" to be array'));
            }

            foreach ($targetingPlatforms as $targetingPlatform) {
                if (!$this->isSupportedTargetingPlatform($targetingPlatform)) {
                    throw new InvalidArgumentException(sprintf('not supported targeting platform "%s"', $targetingPlatform));
                }
            }
        }
    }

    /**
     * check if is Supported Targeting Player Size
     *
     * @param $playerSize
     * @return bool
     */
    private function isSupportedTargetingPlayerSize($playerSize)
    {
        return (in_array($playerSize, self::$TARGETING_SUPPORTED_PLAYER_SIZES));
    }

    /**
     * check if is Supported Targeting Required Macro
     *
     * @param $requiredMacro
     * @return bool
     */
    private function isSupportedTargetingRequiredMacro($requiredMacro)
    {
        return (in_array($requiredMacro, self::$TARGETING_SUPPORTED_REQUIRED_MACROS));
    }

    /**
     * check if is Supported Targeting Platform
     *
     * @param $platform
     * @return bool
     */
    private function isSupportedTargetingPlatform($platform)
    {
        return (in_array($platform, self::$TARGETING_SUPPORTED_PLATFORMS));
    }
}