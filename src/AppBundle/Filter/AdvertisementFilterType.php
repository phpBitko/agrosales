<?php

namespace AppBundle\Filter;

use AppBundle\Entity\DirPurpose;
use AppBundle\Form\Type\IntegerRangeFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;


class AdvertisementFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('area', Filters\NumberRangeFilterType::class, [
            'left_number_options' => [
                'scale' => 4,
                'condition_operator' => FilterOperands::OPERATOR_GREATER_THAN,
                'constraints' => new Range([
                    'min' => 0,
                    'groups' => 'filtering',
                    'minMessage' => 'Значення має бути не менше {{ limit }}',
                ]),
            ],
            'right_number_options' => [
                'scale' => 4,
                'condition_operator' => FilterOperands::OPERATOR_LOWER_THAN,
                'constraints' => new Range([
                    'min' => 0,
                    'groups' => 'filtering',
                    'minMessage' => 'Значення має бути не менше {{ limit }}',
                ]),
            ],
        ]);

        $builder->add('addDate', Filters\DateTimeRangeFilterType::class, [
            'left_datetime_options' => [
                'widget' => 'single_text',
                'label' => ' ',
                'format' => 'dd.MM.yyyy',
                'attr' => [
                    'class' => 'datepicker',
                    'data-date-format' => 'dd.mm.yyyy',
                ]
            ],
            'right_datetime_options' => [
                'widget' => 'single_text',
                'label' => ' ',
                'format' => 'dd.MM.yyyy',
                'attr' => [
                    'class' => 'datepicker ',
                    'data-date-format' => 'dd.mm.yyyy',
                ],
            ]
        ]);

        $builder->add('price', IntegerRangeFilterType::class, [
            'left_number_options' => [
                'condition_operator' => FilterOperands::OPERATOR_GREATER_THAN,
                'constraints' => [new Range([
                    'min' => 0,
                    'max' => 2147483647,
                    'groups' => 'filtering',
                    'minMessage' => 'Значення має бути не менше {{ limit }}',
                    'maxMessage' => 'Значення має бути не більше {{ limit }}',
                ])
                ],
            ],
            'right_number_options' => [
                'condition_operator' => FilterOperands::OPERATOR_LOWER_THAN,
                'constraints' => new Range([
                    'min' => 0,
                    'max' => 2147483647,
                    'groups' => 'filtering',
                    'minMessage' => 'Значення має бути не менше {{ limit }}',
                    'maxMessage' => 'Значення має бути не більше {{ limit }}',
                ])
            ],
        ]);
        $builder->add('isHouse', Filters\CheckboxFilterType::class, [
            'label' => 'Будинок',
            'attr' => ['class' => ''],
            'required' => false,
            'label_attr' => ['class' => 'font-weight-bold'],
        ]);

        $builder->add('isElectricity', Filters\CheckboxFilterType::class, [
            'label' => 'Електрика',
            'attr' => ['class' => ''],
            'required' => false,
            'label_attr' => ['class' => 'font-weight-bold'],
        ]);
        $builder->add('isWaterSupply', Filters\CheckboxFilterType::class, [
            'label' => 'Водозабезпечення',
            'attr' => ['class' => ''],
            'label_attr' => ['class' => 'font-weight-bold'],
        ]);
        $builder->add('isRoad', Filters\CheckboxFilterType::class, [
            'label' => 'Дорога з твердим покриттям',
            'attr' => ['class' => ''],
            'label_attr' => ['class' => 'font-weight-bold'],
        ]);
        $builder->add('isSewerage', Filters\CheckboxFilterType::class, [
            'label' => 'Каналізація',
            'attr' => ['class' => ''],
            'label_attr' => ['class' => 'font-weight-bold'],
        ]);
        $builder->add('isGas', Filters\CheckboxFilterType::class, [
            'label' => 'Газ',
            'attr' => ['class' => ''],
            'label_attr' => ['class' => 'font-weight-bold'],
        ]);
        $builder->add('dirPurpose', Filters\EntityFilterType::class, [
            'label' => 'Цільове призначення',
            'multiple' => true,
            'class' => DirPurpose::class,
            'attr' => ['class' => 'form-control'],
            'choice_label' => function ($purpose) {
                return $purpose->getCode() . ' ' . $purpose->getText();
            },
        ]);
    }

    public function getBlockPrefix()
    {
        return 'item_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'validation_groups' => array('filtering'), // avoid NotBlank() constraint-related message
        ));
    }

}