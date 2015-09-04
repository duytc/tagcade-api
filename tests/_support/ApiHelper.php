<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class ApiHelper extends \Codeception\Module
{
    private $token;

    private $adminToken;

    public function _beforeSuite($settings = array())
    {
        $this->_initParams($settings);

        $url = $settings['modules']['config']['REST']['url'] . URL_API . '/getToken';

        if ($this->adminToken === null) {
            $admin = $settings['modules']['login']['admin']['username'];
            $adminPassword = $settings['modules']['login']['admin']['password'];

            $this->adminToken = $this->_getToken($url, $admin, $adminPassword);
        }

        if ($this->token === null) {
            $user = $settings['modules']['login']['publisher']['username'];
            $userPassword = $settings['modules']['login']['publisher']['password'];

            $this->token = $this->_getToken($url, $user, $userPassword);
        }
    }

    private function _initParams($settings = array())
    {
        if (!defined('PARAMS_PUBLISHER')) {
            define('PARAMS_PUBLISHER', $settings['modules']['params']['publisher']);
        }

        if (!defined('PARAMS_AD_NETWORK')) {
            define('PARAMS_AD_NETWORK', $settings['modules']['params']['adNetwork']);
        }

        if (!defined('PARAMS_SITE')) {
            define('PARAMS_SITE', $settings['modules']['params']['site']);
        }

        if (!defined('PARAMS_AD_SLOT')) {
            define('PARAMS_AD_SLOT', $settings['modules']['params']['adSlot']);
        }

        if (!defined('PARAMS_NATIVE_AD_SLOT')) {
            define('PARAMS_NATIVE_AD_SLOT', $settings['modules']['params']['nativeAdSlot']);
        }

        if (!defined('PARAMS_DYNAMIC_AD_SLOT')) {
            define('PARAMS_DYNAMIC_AD_SLOT', $settings['modules']['params']['dynamicAdSlot']);
        }

        if (!defined('PARAMS_EXPECTED_AD_SLOT')) {
            define('PARAMS_EXPECTED_AD_SLOT', $settings['modules']['params']['expectedAdSlot']);
        }

        if (!defined('PARAMS_EXPECTED_AD_SLOT_2')) {
            define('PARAMS_EXPECTED_AD_SLOT_2', $settings['modules']['params']['expectedAdSlot_2']);
        }

        if (!defined('PARAMS_DEFAULT_AD_SLOT')) {
            define('PARAMS_DEFAULT_AD_SLOT', $settings['modules']['params']['defaultAdSlot']);
        }

        if (!defined('PARAMS_AD_TAG')) {
            define('PARAMS_AD_TAG', $settings['modules']['params']['adTag']);
        }

        /* library feature */
        if (!defined('PARAMS_LIBRARY_AD_SLOT')) {
            define('PARAMS_LIBRARY_AD_SLOT', $settings['modules']['params']['libraryAdSlot']);
        }

        if (!defined('PARAMS_LIBRARY_DISPLAY_AD_SLOT')) {
            define('PARAMS_LIBRARY_DISPLAY_AD_SLOT', $settings['modules']['params']['libraryDisplayAdSlot']);
        }

        if (!defined('PARAMS_LIBRARY_NATIVE_AD_SLOT')) {
            define('PARAMS_LIBRARY_NATIVE_AD_SLOT', $settings['modules']['params']['libraryNativeAdSlot']);
        }

        if (!defined('PARAMS_LIBRARY_DYNAMIC_AD_SLOT')) {
            define('PARAMS_LIBRARY_DYNAMIC_AD_SLOT', $settings['modules']['params']['libraryDynamicAdSlot']);
        }

        if (!defined('PARAMS_LIBRARY_DEFAULT_AD_SLOT')) {
            define('PARAMS_LIBRARY_DEFAULT_AD_SLOT', $settings['modules']['params']['libraryDefaultAdSlot']);
        }

        if (!defined('PARAMS_LIBRARY_EXPECTED_AD_SLOT')) {
            define('PARAMS_LIBRARY_EXPECTED_AD_SLOT', $settings['modules']['params']['libraryExpectedAdSlot']);
        }

        if (!defined('PARAMS_LIBRARY_EXPECTED_AD_SLOT_2')) {
            define('PARAMS_LIBRARY_EXPECTED_AD_SLOT_2', $settings['modules']['params']['libraryExpectedAdSlot_2']);
        }

        if (!defined('PARAMS_LIBRARY_AD_TAG')) {
            define('PARAMS_LIBRARY_AD_TAG', $settings['modules']['params']['libraryAdTag']);
        }
        /* end - library feature */

        /* channel feature */
        if (!defined('PARAMS_CHANNEL')) {
            define('PARAMS_CHANNEL', $settings['modules']['params']['channel']);
        }
        /* end - channel feature */
    }

    private function _getToken($url, $username, $password)
    {
        $ch = curl_init();
        $fields = array( 'username' => $username, 'password' => $password);
        $postvars = '';
        foreach($fields as $key=>$value) {
            $postvars .= $key . "=" . $value . "&";
        }

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);

        $responseArr = json_decode($response, true);

        curl_close ($ch);

        return $responseArr['token'];
    }

    public function getToken() {
        return $this->token;
    }

    public function getAdminToken() {
        return $this->adminToken;
    }
}
