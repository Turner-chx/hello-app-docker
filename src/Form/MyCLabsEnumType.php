<?php
/**
 * Created by PhpStorm.
 * User: maxencebeno
 * Date: 2019-02-06
 * Time: 15:18
 */

namespace App\Form;

use App\Enum\EnumTranslatable;
use Exception;
use InvalidArgumentException;
use MyCLabs\Enum\Enum;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyCLabsEnumType extends ChoiceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $enumFQCN = $options['enum'];
        if (false === is_subclass_of($enumFQCN, Enum::class)) {
            throw new InvalidArgumentException('enum option must be a FQCN of class implements MyCLabs\Enum\Enum;');
        }

        $builder->addModelTransformer(new CallbackTransformer(
            function ($dataFromModel) use ($enumFQCN) {
                if ($dataFromModel instanceof Enum) {
                    return $dataFromModel->getValue();
                }
                if (true === $enumFQCN::isValid($dataFromModel)) {
                    return $dataFromModel;
                }
                if (true === is_array($dataFromModel)) {
                    $res = [];
                    foreach ($dataFromModel as $enum) {
                        if ($enum instanceof Enum) {
                            $res [] = $enum->getValue();
                        }
                    }
                    return $res;
                }
                return null;
            },
            function ($dataFromForm) use ($enumFQCN) {
                try {
                    if (true === $enumFQCN::isValid($dataFromForm)) {
                        return new $enumFQCN($dataFromForm);
                    }
                    if (true === is_array($dataFromForm)) {
                        $res = [];
                        foreach ($dataFromForm as $value) {
                            if (true === $enumFQCN::isValid($value)) {
                                $res [] = new $enumFQCN($value);
                            }
                        }
                        return $res;
                    }
                } catch (Exception $e) {
                }
                return null;
            }
        ));

        $choices = [];
        /**
         * @var Enum[] $enums
         * @var string $enumFQCN
         */
        $enums = call_user_func([$enumFQCN, 'values']);
        foreach ($enums as $enum) {
            $key = ($enum instanceof EnumTranslatable) ? $enum->getTranslationKey() : $enum->getValue();
            $choices [$key] = $enum->getValue();
        }
        $options['choices'] = $choices;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('enum');
        parent::configureOptions($resolver);
    }

}