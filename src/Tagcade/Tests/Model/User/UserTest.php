<?php

namespace Tagcade\Tests\Model\User;

use InvalidArgumentException;
use Tagcade\Model\User\Role\Publisher;
use Tagcade\Test\TestCase;

class UserTest extends TestCase
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
}