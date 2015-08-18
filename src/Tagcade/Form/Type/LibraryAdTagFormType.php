<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;

class LibraryAdTagFormType extends AbstractRoleSpecificFormType
{

    const AD_TYPE_HTML = 0;
    const AD_TYPE_IMAGE = 1;

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
            $builder->add('adNetwork');

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
            ->add('name')
            ->add('id')
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event){
                /** @var LibraryAdTagInterface $libraryAdTag */
                $libraryAdTag = $event->getData();

                try {
                    switch ($libraryAdTag->getAdType()) {
                        case self::AD_TYPE_IMAGE:
                            $this->validateImageAd($libraryAdTag);
                            break;
                        default:
                            $this->validateCustomAd($libraryAdTag);
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
                'data_class' => LibraryAdTag::class,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_library_ad_tag';
    }
}