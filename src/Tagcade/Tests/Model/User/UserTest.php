<?php

namespace Tagcade\Tests\Model\User;

use InvalidArgumentException;
use Tagcade\Model\User\Publisher;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testCorrectRoles()
    {
        $user = $this->getUser(['ROLE_PUBLISHER']);
        $publisher = new Publisher($user);

        $this->assertTrue($publisher instanceof Publisher);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIncorrectRoles()
    {
        $user = $this->getUser(['ROLE_ADMIN']);
        $publisher = new Publisher($user);
    }

    protected function getUser(array $roles)
    {
        $user = $this->getMock('Tagcade\Model\User\UserInterface');

        $user->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        return $user;
    }
}