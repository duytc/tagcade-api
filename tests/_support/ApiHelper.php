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
