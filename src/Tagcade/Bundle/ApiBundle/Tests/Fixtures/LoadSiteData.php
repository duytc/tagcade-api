<?php

namespace Tagcade\Bundle\ApiBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Entity\Site;
use Tagcade\Model\User\Role\Publisher;

class LoadSiteData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var \Tagcade\Bundle\UserBundle\Entity\User $user */
        $user = $this->getReference('test-user-publisher');
        $publisher = new Publisher($user);

        $site = new Site();
        $site->setName('my test');
        $site->setDomain('mytest.com');
        $site->setPublisher($publisher);

        $site2 = new Site();
        $site2->setName('another test');
        $site2->setDomain('anothertest.com');
        $site2->setPublisher($publisher);

        $manager->persist($site);
        $manager->persist($site2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}