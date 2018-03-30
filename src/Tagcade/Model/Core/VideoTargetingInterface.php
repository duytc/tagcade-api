<?php

namespace Tagcade\Model\Core;


interface VideoTargetingInterface
{
    /* targeting keys */
    const TARGETING_KEY_REQUIRED_MACROS = 'required_macros';
    const TARGETING_KEY_PLAYER_SIZE = 'player_size';
    const TARGETING_KEY_DOMAINS = 'domains'; // known as WhiteListDomain
    const TARGETING_KEY_EXCLUDE_DOMAINS = 'exclude_domains'; // known as BlackListDomain
    const TARGETING_KEY_COUNTRIES = 'countries';
    const TARGETING_KEY_EXCLUDE_COUNTRIES = 'exclude_countries';
    const TARGETING_KEY_PLATFORM = 'platform';

    /* targeting value for player_size */
    const TARGETING_PLAYER_SIZE_LARGE = 'large';
    const TARGETING_PLAYER_SIZE_MEDIUM = 'medium';
    const TARGETING_PLAYER_SIZE_SMALL = 'small';

    /* targeting value for required_macros */
    const TARGETING_REQUIRED_MACRO_IP_ADDRESS = 'ip_address';
    const TARGETING_REQUIRED_MACRO_USER_AGENT = 'user_agent';
    const TARGETING_REQUIRED_MACRO_PAGE_URL = 'page_url';
    const TARGETING_REQUIRED_MACRO_DOMAIN = 'domain';
    const TARGETING_REQUIRED_MACRO_PAGE_TITLE = 'page_title';
    const TARGETING_REQUIRED_MACRO_PLAYER_WIDTH = 'player_width';
    const TARGETING_REQUIRED_MACRO_PLAYER_HEIGHT = 'player_height';
    const TARGETING_REQUIRED_MACRO_PLAYER_DIMENSIONS = 'player_dimensions';
    const TARGETING_REQUIRED_MACRO_PLAYER_SIZE = 'player_size';
    const TARGETING_REQUIRED_MACRO_VIDEO_DURATION = 'video_duration';
    const TARGETING_REQUIRED_MACRO_VIDEO_URL = 'video_url';
    const TARGETING_REQUIRED_MACRO_VIDEO_ID = 'video_id';
    const TARGETING_REQUIRED_MACRO_VIDEO_TITLE = 'video_title';
    const TARGETING_REQUIRED_MACRO_VIDEO_DESCRIPTION = 'video_description';
    const TARGETING_REQUIRED_MACRO_APP_NAME = 'app_name';
    const TARGETING_REQUIRED_MACRO_USER_LAT = 'user_lat';
    const TARGETING_REQUIRED_MACRO_USER_LON = 'user_lon';
    // new macros - 2018/08/18
    const TARGETING_REQUIRED_MACRO_COUNTRY = 'country';
    const TARGETING_REQUIRED_MACRO_TIMESTAMP = 'timestamp';
    const TARGETING_REQUIRED_MACRO_WATERFALL_ID = 'waterfall_id';
    const TARGETING_REQUIRED_MACRO_DEMAND_TAG_ID = 'demand_tag_id';
    const TARGETING_REQUIRED_MACRO_DEVICE_ID = 'device_id';
    const TARGETING_REQUIRED_MACRO_DEVICE_NAME = 'device_name';
    const TARGETING_REQUIRED_MACRO_DEMAND_SELL_PRICE = 'demand_sell_price';
    // macros for debugging
    const TARGETING_REQUIRED_MACRO__DEBUG = '_debug'; // not yet used, only for debug
    const TARGETING_REQUIRED_MACRO__ENV = '_env'; // not yet used, only for debug

    /* targeting value for platform */
    const TARGETING_PLATFORM_FLASH = 'flash';
    const TARGETING_PLATFORM_JS = 'js';

    /**
     * @return array supported targeting keys
     */
    public static function getSupportedTargetingKeys();
}