<?php

namespace Tagcade\Form\Type;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\DisplayWhiteList;
use Tagcade\Entity\Core\NetworkBlacklist;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\Core\NetworkWhiteListInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Service\StringUtilTrait;

class DisplayWhiteListFormType extends AbstractRoleSpecificFormType
{
    use StringUtilTrait;

    protected $em;
    function __construct(ObjectManager $om)
    {
        $this->em = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('name')
            ->add('domains');

        $builder->add('networkWhiteLists', 'collection', array(
                'mapped' => true,
                'type' => new NetworkWhiteListFormType(),
                'allow_add' => true,
                'allow_delete' => true,
            )
        );

        if ($this->userRole instanceof AdminInterface) {
            $builder->add(
                $builder->create('publisher')
                    ->addModelTransformer(
                        new RoleToUserEntityTransformer(), false
                    )
            );
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var DisplayWhiteListInterface $displayWhiteList */
                $displayWhiteList = $event->getData();

                //validate domains
                $domains = $displayWhiteList->getDomains();
                if (!is_array($domains)) {
                    throw new InvalidArgumentException(sprintf('expect "domains" to be an array, got "%s"', gettype($domains)));
                }

                $filterDomains = [];
                foreach ($domains as $domain) {
                    $filterDomains[] = $this->extractDomainAllowWildcard($domain);
                }
                $filterDomains = array_map(function($domain) {
                    return strtolower($domain);
                }, $filterDomains);
                $filterDomains = array_values(array_unique($filterDomains));
                $displayWhiteList->setDomains($filterDomains);

                /** @var Collection| NetworkWhiteListInterface[] $networkWhiteLists */
                $networkWhiteLists = $event->getForm()->get('networkWhiteLists')->getData();

                $networkBlacklistRepository = $this->em->getRepository(NetworkBlacklist::class);
                foreach ($networkWhiteLists as $networkWhiteList) {
                    $networkBlacklists = $networkBlacklistRepository->getForAdNetwork($networkWhiteList->getAdNetwork());
                    if (count($networkBlacklists) > 0) {
                        throw new InvalidArgumentException('Demand partner can not have both blacklist and white list');
                    }

                    if (!$networkWhiteList->getDisplayWhiteList() instanceof DisplayWhiteListInterface) {
                        $networkWhiteList->setDisplayWhiteList($displayWhiteList);
                    }
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => DisplayWhiteList::class
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_display_blacklist';
    }
}