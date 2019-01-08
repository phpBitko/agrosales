<?php

namespace AppBundle\Filter;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class AdvertisementFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $purpose = $options['purpose'];
       // $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();
        $builder->add('area', Filters\NumberRangeFilterType::class);

/*        $builder->add('addDate', Filters\DateTimeRangeFilterType::class, array(
            'left_datetime_options' => array(
                'label' => 'from',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => array('class' => 'date date-filter-left')),
            'right_datetime_options' => array(
                'label' => 'to',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => array('class' => 'date date-filter-right')),
        ));*/

        $builder->add('price', Filters\NumberRangeFilterType::class);
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
        /*$builder->add('dirPurpose', Filters\ChoiceFilterType::class, [
            'label' => 'Цільове призначення',
            'multiple' => true,
            'choices' => $purpose,
            'choice_label' => function ($purpose, $key, $index) {
                return ("{$purpose->getCode()} {$purpose->getText()}");
            },
        ]);*/

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
        $resolver->setRequired(array('purpose'));

    } 

}