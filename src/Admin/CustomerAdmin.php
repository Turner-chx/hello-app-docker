<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Customer;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

final class CustomerAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name', null, ['label' => 'app.entity.Customer.field.name'])
            ->add('email', null, ['label' => 'app.entity.Customer.field.email'])
            ->add('address', null, ['label' => 'app.entity.Customer.field.address'])
            ->add('additionalAddress', null, ['label' => 'app.entity.Customer.field.additionalAddress'])
            ->add('postalCode', null, ['label' => 'app.entity.Customer.field.postalCode'])
            ->add('city', null, ['label' => 'app.entity.Customer.field.city'])
            ->add('phoneNumber', null, ['label' => 'app.entity.Customer.field.phoneNumber'])
            ->add('createdAt', 'doctrine_orm_datetime', [
                'field_type' => DateTimePickerType::class,
                'label' => 'app.entity.Customer.field.createdAt'
            ])
            ->add('country', 'doctrine_orm_choice', [
                    'global_search' => true,
                    'field_type' => CountryType::class,
                    'label' => 'app.entity.Customer.field.country',
                    'preferred_choices' => ['FR']
            ])
            ->add('customerContact', null, ['label' => 'app.entity.Customer.field.customerContact'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('name', null, ['label' => 'app.entity.Customer.field.name'])
            ->add('email', null, ['label' => 'app.entity.Customer.field.email'])
            ->add('address', null, ['label' => 'app.entity.Customer.field.address'])
            ->add('additionalAddress', null, ['label' => 'app.entity.Customer.field.additionalAddress'])
            ->add('postalCode', null, ['label' => 'app.entity.Customer.field.postalCode'])
            ->add('city', null, ['label' => 'app.entity.Customer.field.city'])
            ->add('phoneNumber', null, ['label' => 'app.entity.Customer.field.phoneNumber'])
            ->add('createdAt', null, ['label' => 'app.entity.Customer.field.createdAt'])
            ->add('country', CountryType::class, ['label' => 'app.entity.Customer.field.country'])
            ->add('customerContact', null, ['label' => 'app.entity.Customer.field.customerContact']);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        /** @var Customer $subject */
        $subject = $this->getSubject();
        $domTom = '';
        if (null !== $subject) {
            if ($subject->isDomTomOrSwitzerland()) {
                $domTom = 'red-color';
            }
        }
        $formMapper
            ->add('name', null, [
                'label' => 'app.entity.Customer.field.name',
                'attr' => [
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'
                ],
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'app.entity.Customer.field.email',
                'attr' => [
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'
                ],
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ->add('address', null, [
                'label' => 'app.entity.Customer.field.address',
                'attr' => [
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'
                ],
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ->add('additionalAddress', null, [
                'label' => 'app.entity.Customer.field.additionalAddress',
                'attr' => [
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'
                ],
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ->add('postalCode', null, [
                'label' => 'app.entity.Customer.field.postalCode',
                'attr' => [
                    'class' => 'checkPostCode'
                ],
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ->add('city', null, [
                'label' => 'app.entity.Customer.field.city',
                'attr' => [
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();',
                    'class' => 'addCity'
                ],
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'app.entity.Customer.field.phoneNumber',
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'app.entity.Customer.field.country',
                'preferred_choices' => ['FR'],
                'attr' => [
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'
                ],
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ->add('customerContact', null, [
                'label' => 'app.entity.Customer.field.customerContact',
                'attr' => [
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'
                ],
                'label_attr' => [
                    'class' => $domTom
                ]
            ])
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
    }
}
