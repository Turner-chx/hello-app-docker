<?php

namespace App\Form;

use App\Entity\Files;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class FilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichFileType::class, [
                'required' => false,
                'allow_delete' => false,
                'label' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '15M',
//                        'mimeTypes' => [
//                            'application/pdf',
//                            'image/gif',
//                            'image/png',
//                            'image/jpeg',
//                            'image/jpg',
//                            'application/msword',
//                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
//                            'application/vnd.ms-excel',
//                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
//                            'text/csv',
//                            'text/plain'
//                        ]
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Files::class,
        ]);
    }
}
