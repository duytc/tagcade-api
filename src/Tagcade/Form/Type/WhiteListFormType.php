<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\WhiteList;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\WhiteListInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Service\StringUtilTrait;

class WhiteListFormType extends AbstractRoleSpecificFormType
{
    use StringUtilTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('domains');

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
                /** @var WhiteListInterface $whiteList */
                $whiteList = $event->getData();

                //validate domains
                $domains = $whiteList->getDomains();
                if (!is_array($domains)) {
                    throw new InvalidArgumentException(sprintf('expect "domains" to be an array, got "%s"', gettype($domains)));
                }

                foreach($domains as &$domain) {
                    $domain = $this->extractDomain($domain);
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => WhiteList::class
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_white_list';
    }
}