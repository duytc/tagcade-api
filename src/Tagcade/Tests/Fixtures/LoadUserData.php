<?php

namespace Tagcade\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Bundle\UserSystem\AdminBundle\Entity\User as Admin;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User as Publisher;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        /** @var \Rollerworks\Bundle\MultiUserBundle\Model\DelegatingUserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->getUserDiscriminator()->setCurrentUser('tagcade_user_system_admin');

        $super_admin = $userManager->createUser()
            ->setUsername('superadmin')
            ->setEmail('superadmin@tagcade.com')
            ->setPlainPassword('12345')
            ->setEnabled(true)
            ->setRoles(['ROLE_ADMIN'])
        ;
        $userManager->updateUser($super_admin);

        $admin = $userManager->createUser()
            ->setUsername('admin')
            ->setEmail('admin@tagcade.com')
            ->setPlainPassword('12345')
            ->setEnabled(true)
            ->setRoles(['ROLE_ADMIN'])
        ;
        $userManager->updateUser($admin);

        $userManager->getUserDiscriminator()->setCurrentUser('tagcade_user_system_publisher');
        $publisher = $userManager->createUser()
            ->setUsername('pub')
            ->setEmail('pub@tagcade.com')
            ->setPlainPassword('12345')
            ->setEnabled(true)
            ->setRoles(['ROLE_PUBLISHER'])
        ;
        $userManager->updateUser($publisher);

        $publisher2 = $userManager->createUser()
            ->setUsername('pub2')
            ->setEmail('pub2@tagcade.com')
            ->setPlainPassword('12345')
            ->setEnabled(true)
            ->setRoles(['ROLE_PUBLISHER'])
        ;
        $userManager->updateUser($publisher2);

        $this->addReference('test-user-super-admin', $super_admin);
        $this->addReference('test-user-admin', $admin);
        $this->addReference('test-user-publisher1', $publisher);
        $this->addReference('test-user-publisher2', $publisher2);
    }

    public function getOrder()
    {
        return 1;
    }

    private function createPublisher($username, $email, $password, array $roles)
    {
        $user = new Publisher();
        $user->setUsername($username);
        $user->setUsernameCanonical($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEnabled(true);

        return $user;
    }

    private function createAdmin($username, $email, $password, array $roles)
    {
        $user = new Admin();
        $user->setUsername($username);
        $user->setUsernameCanonical($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEnabled(true);

        return $user;
    }
}