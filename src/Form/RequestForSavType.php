<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 24/06/19
 * Time: 14:32
 */

namespace App\Form;


use App\Entity\NatureSetting;
use App\Enum\ClientTypeEnum;
use App\Enum\SavTypeEnum;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestForSavType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $source = $options['source'];
        $builder
            ->add('serialNumber', TextType::class, [
                'label' => 'app.entity.Sav.field.serial_number_customer.name',
                'required' => false,
                'attr' => [
                    'data-action' => '/request-for-sav/' . $source . '/get-serial-number'
                ]
            ])
            ->add('clientType', ChoiceType::class, [
                'label' => 'app.entity.Sav.form.you_are',
                'required' => true,
                'choices' => array_flip(ClientTypeEnum::getChoices())
            ])
            ->add('dealerReference', TextType::class, [
                'label' => 'app.entity.Sav.form.your_reference',
                'required' => false
            ])
            ->add('purchaseDate', DateType::class, [
                'label' => 'app.entity.Sav.field.purchase_date.name',
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-mm-dd',
                'attr' => [
                    'class' => 'datepicker'
                ]
            ])
            ->add('store', TextType::class, [
                'label' => 'app.entity.Customer.field.store.name',
                'required' => true
            ])
            ->add('natures', EntityType::class, [
                'label' => 'app.entity.Sav.field.natures_setting.name',
                'required' => true,
                'class' => NatureSetting::class,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function(EntityRepository $er) use ($source){
                    return $er->createQueryBuilder('ns')
                        ->select('ns')
                        ->innerJoin('ns.source', 's')
                        ->where('s.slug = :source')
                        ->setParameter('source', $source);
                },
            ])
            ->add('description', TextareaType::class, [
                'label' => 'app.entity.Sav.field.description.name',
                'required' => true,
            ])
            ->add('customerInfos', CustomerType::class)
            ->add('availability', TextType::class, [
                'label' => 'app.entity.Sav.form.your_availability',
                'required' => true
            ])
            ->add('attachment', FilesType::class, [
                'label' => 'app.entity.Sav.form.proof_of_purchase',
                'required' => true,
            ])
            ->add('imeiNumber', TextType::class, [
                'label' => 'app.entity.Source.form.imei',
                'required' => false,
            ])
            ->add('savType', ChoiceType::class, [
                'label' => 'app.entity.Sav.field.sav_type.name',
                'required' => true,
                'choices' => array_flip(SavTypeEnum::getChoices())
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'app.entity.Sav.form.submit',
                'attr' => [
                    'class' =>'btn submit-button'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'source' => null,
            'csrf_protection' => false
            // Configure your form options here
        ]);
    }
}