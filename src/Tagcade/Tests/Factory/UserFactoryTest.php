<?php

namespace Tagcade\Tests\Factory;

use Tagcade\Factory\UserRoleFactory;
use Tagcade\Test\TestCase;

class UserFactoryTest extends TestCase
{
    public function testReturnPublisher()
    {
        $user = $this->getUser(['ROLE_PUBLISHER']);
        $this->assertInstanceOf('Tagcade\Model\User\Role\Publisher', UserRoleFactory::getRole($user));
    }

    public function testReturnAdmin()
    {
        $user = $this->getUser(['ROLE_ADMIN']);
        $this->assertInstanceOf('Tagcade\Model\User\Role\Admin', UserRoleFactory::getRole($user));
    }

    /**
     * @expectedException \Tagcade\Exception\InvalidUserRoleException
     */
    public function testInvalidRole()
    {
        $user = $this->getUser(['ROLE_INVALID']);
        UserRoleFactory::getRole($user);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoUser()
    {
        UserRoleFactory::getRole(null);
    }
}