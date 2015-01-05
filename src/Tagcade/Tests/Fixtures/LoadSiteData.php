<?php

namespace Tagcade\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Entity\Core\Site;

class LoadSiteData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var \Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User $publisher1 */
        $publisher1 = $this->getReference('test-user-publisher1');
        /** @var \Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User $publisher2 */
        $publisher2 = $this->getReference('test-user-publisher2');

        $site1 = new Site();
        $site1->setName('my test');
        $site1->setDomain('mytest.com');
        $site1->setPublisher($publisher1);

        $site2 = new Site();
        $site2->setName('another test');
        $site2->setDomain('anothertest.com');
        $site2->setPublisher($publisher1);

        $site3 = new Site();
        $site3->setName('one more');
        $site3->setDomain('onemore.com');
        $site3->setPublisher($publisher2);

        $manager->persist($site1);
        $manager->persist($site2);
        $manager->persist($site3);

        $manager->flush();

        $this->addReference('test-site-mytest.com', $site1);
        $this->addReference('test-site-anothertest.com', $site2);
    }

    public function getOrder()
    {
        return 2;
    }
}