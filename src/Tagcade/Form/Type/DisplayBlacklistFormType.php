<?php

namespace Tagcade\Form\Type;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\DisplayBlacklist;
use Tagcade\Entity\Core\NetworkWhiteList;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Service\StringUtilTrait;
use Tagcade\Exception\RuntimeException;

class DisplayBlacklistFormType extends AbstractRoleSpecificFormType
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
            ->add('domains')
        ;

        $builder->add('networkBlacklists', 'collection', array(
                'mapped' => true,
                'type' => new NetworkBlacklistFormType(),
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
                /** @var DisplayBlacklistInterface $displayBlacklist */
                $displayBlacklist = $event->getData();

                //validate domains
                $domains = $displayBlacklist->getDomains();
                if (!is_array($domains)) {
                    throw new InvalidArgumentException(sprintf('expect "domains" to be an array, got "%s"', gettype($domains)));
                }

                $filterDomains = [];
                foreach ($domains as $domain) {
                    $filterDomains[] = $this->extractDomain($domain);
                }
                $displayBlacklist->setDomains($filterDomains);

                /** @var Collection| NetworkBlacklistInterface[] $networkBlacklists */
                $networkBlacklists = $event->getForm()->get('networkBlacklists')->getData();

                $networkWhiteListRepository = $this->em->getRepository(NetworkWhiteList::class);
                foreach ($networkBlacklists as $networkBlacklist) {
                    $networkWhiteLists = $networkWhiteListRepository->getForAdNetwork($networkBlacklist->getAdNetwork());
                    if (count($networkWhiteLists) > 0) {
                        throw new RuntimeException('Demand partner can not have both blacklist and white list');
                    }

                    if (!$networkBlacklist->getDisplayBlacklist() instanceof DisplayBlacklistInterface) {
                        $networkBlacklist->setDisplayBlacklist($displayBlacklist);
                    }
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => DisplayBlacklist::class,
                'allow_extra_fields' => true,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_display_blacklist';
    }
}