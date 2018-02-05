<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;

class LibraryAdTagFormType extends AbstractRoleSpecificFormType
{
    /** @var UserRoleInterface $userRole */
    protected $userRole;

    public function __construct($userRole = null)
    {
        if ($userRole != null && $userRole instanceof UserRoleInterface) {
            $this->userRole = $userRole;
        }
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {
            // allow all sites, default is fine
            $builder->add('adNetwork', 'entity', array(
                    'class' => AdNetwork::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('nw')->select('nw'); }
                ));

        } else if ($this->userRole instanceof PublisherInterface) {
            $publisher = $this->userRole;

            $builder
                ->add('adNetwork', 'entity', array(
                    'class' => AdNetwork::class,
                    'query_builder' => function (AdNetworkRepositoryInterface $repository) use ($publisher) {
                        return $repository->getAdNetworksForPublisherQuery($publisher);
                    },
                ));

            unset($publisher);
            
        } else {
            throw new LogicException('A valid user role is required by AdSlotFormType');
        }

        $builder
            ->add('html')
            ->add('visible')
            ->add('adType')
            ->add('descriptor')
            ->add('inBannerDescriptor')
            ->add('name')
            ->add('id')
            ->add('expressionDescriptor')
            ->add('sellPrice')
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event){
                /** @var LibraryAdTagInterface $libraryAdTag */
                $libraryAdTag = $event->getData();

                if (!empty($libraryAdTag->getExpressionDescriptor())) {
                    // validate expression descriptor
                    try {
                        ExpressionInJsGenerator::validateExpressionDescriptor($libraryAdTag->getExpressionDescriptor());
                    } catch (\Exception $e) {
                        $libraryAdTag->setExpressionDescriptor(null);
                    }
                }

                try {
                    switch ($libraryAdTag->getAdType()) {
                        case LibraryAdTag::AD_TYPE_IMAGE:
                            $this->validateImageAd($libraryAdTag);
                            break;
                        case LibraryAdTag::AD_TYPE_THIRD_PARTY:
                            $this->validateCustomAd($libraryAdTag);
                            break;
                        case LibraryAdTag::AD_TYPE_IN_BANNER:
                            $this->validateInBanner($libraryAdTag);
                    }
                } catch (InvalidFormException $ex) {
                    $form = $event->getForm();

                    $form->get('descriptor')->addError(new FormError($ex->getMessage()));
                }
            }
        );
    }

    protected function validateImageAd(LibraryAdTagInterface $libraryAdTag)
    {
        $descriptor = $libraryAdTag->getDescriptor();

        if (null === $descriptor || !is_array($descriptor) || !isset($descriptor['imageUrl']) || !isset($descriptor['targetUrl']))
        {
            throw new InvalidFormException('The descriptor "%descriptor%" for AD_TYPE_IMAGE invalid: must contain keys \'imageUrl\' and \'targetUrl\'.', $this);
        }

        $this->validateImageUrl($descriptor['imageUrl']);

        $this->validateTargetUrl($descriptor['targetUrl']);
    }

    protected function validateCustomAd(LibraryAdTagInterface $libraryAdTag)
    {
        if (null === $libraryAdTag->getHtml()) {
            throw new InvalidFormException('expect html of ad tag');
        }
    }

    protected function validateInBanner(LibraryAdTagInterface $libraryAdTag)
    {
        if(!$libraryAdTag->getAdNetwork()->getPublisher()->hasInBannerModule()) {
            throw new InvalidArgumentException('module In-Banner need to be enabled for other modules to be enabled.');
        }

        $inBannerDescriptor = $libraryAdTag->getInBannerDescriptor();

        if(count($inBannerDescriptor['vastTags']) == 0) {
            throw new InvalidFormException('VastTag value should not be blank');
        }

        foreach($inBannerDescriptor['vastTags'] as $vastTag) {
            if (!is_array($vastTag)) {
                throw new InvalidFormException('invalid vastTag value');
            }

            if (!array_key_exists('tag', $vastTag)) {
                throw new InvalidFormException('invalid vastTag value');
            }
        }
    }

    /**
     * validate ImageUrl.
     * @param $imageUrl
     */
    protected function validateImageUrl($imageUrl)
    {
        if (null === $imageUrl || sizeof($imageUrl) < 0) {
            throw new InvalidFormException('The descriptor for AD_TYPE_IMAGE invalid: \'imageUrl\' must not null"', $this);
        }

        $this->validateUrl($imageUrl);
    }

    /**
     * validate TargetUrl
     * @param $targetUrl
     */
    protected function validateTargetUrl($targetUrl)
    {
        $this->validateUrl($targetUrl);
    }

    /**
     * validate Url format
     * @param $url
     */
    protected function validateUrl($url)
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)){
            throw new InvalidFormException('The format of url "%url%" is invalid.', $this);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => LibraryAdTag::class,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_library_ad_tag';
    }
}