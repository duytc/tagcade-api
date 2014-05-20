<?php

namespace Tagcade\Test;

use Tagcade\Model\User\UserInterface;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $roles
     * @return UserInterface
     */
    protected function getUser(array $roles)
    {
        $user = $this->getMock('Tagcade\Model\User\UserInterface');

        $user->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        $user->expects($this->any())
            ->method('hasRole')
            ->will($this->returnCallback(function($subject) use($roles) {
                return(in_array($subject, $roles));
            }));
        ;

        return $user;
    }
}