<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class UpdateExchangeParameterCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:exchange-param:update')
            ->setDescription('Update caches when exchange parameter is updated')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PublisherManagerInterface $publisherManager */
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $publishers = $publisherManager->allActivePublishers();

        $exchanges = $this->getContainer()->getParameter('rtb.exchanges');
        $exchanges = array_map(function(array $exchange) {
            return $exchange['canonicalName'];
        }, $exchanges);

        foreach($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                throw new \Exception('That publisher does not exist');
            }

            if ($publisher->hasRtbModule()) {
                /** @var array $publisherExchanges */
                $publisherExchanges = $publisher->getExchanges();

                $publisherExchanges = array_filter($publisherExchanges, function($ex) use ($exchanges){
                   return in_array($ex, $exchanges);
                });
                $publisher->setExchanges($publisherExchanges);
                $publisherManager->save($publisher);
            }
        }
    }
}