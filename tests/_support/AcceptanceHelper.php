<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AcceptanceHelper extends \Codeception\Module
{
    protected $publisherId;

    public function _beforeSuite($settings = array())
    {
        $this->publisherId = $settings['modules']['params']['publisher'];
    }

    public function getPublisherId()
    {
        return $this->publisherId;
    }
}
