<?php

namespace Tagcade\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Bundle\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $super_admin = $this->createUser('superadmin', 'greg@tagcade.com', '12345', ['ROLE_SUPER_ADMIN']);
        $admin = $this->createUser('admin', 'greg@tagcade.com', '12345', ['ROLE_ADMIN']);
        $publisher = $this->createUser('pub', 'greg@tagcade.com', '12345', ['ROLE_PUBLISHER']);
        $publisher2 = $this->createUser('pub2', 'greg@tagcade.com', '12345', ['ROLE_PUBLISHER']);

        $manager->persist($super_admin);
        $manager->persist($admin);
        $manager->persist($publisher);
        $manager->persist($publisher2);

        $manager->flush();

        $this->addReference('test-user-super-admin', $super_admin);
        $this->addReference('test-user-admin', $admin);
        $this->addReference('test-user-publisher1', $publisher);
        $this->addReference('test-user-publisher2', $publisher2);
    }

    public function getOrder()
    {
        return 1;
    }

    private function createUser($username, $email, $password, array $roles)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEnabled(true);

        return $user;
    }
}