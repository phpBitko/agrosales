<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 17.08.2018
 * Time: 12:55
 */

namespace AppBundle\Form;

use AppBundle\Entity\Advertisement;

use AppBundle\Entity\DirPurpose;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class AdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();

        $builder
            ->add('textHead', TextType::class, [
                'label' => 'Заголовок*',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => true
            ])
            ->add('textAbout', TextareaType::class, [
                'label' => 'Опис*',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => true
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Вартість, грн',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => true
            ])
            ->add('declarantPhoneNum', TelType::class, [
                'label' => 'Телефон',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('area', NumberType::class, [
                'label' => 'Площа',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('areaUnit', ChoiceType::class, [
                'label' => 'Одиниця виміру площі',
                'choices' => ['га' => '1', 'соток' => '2', 'м2' => '3'],
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => true
            ])
            ->add('dirPurpose', EntityType::class, [
                'class' => DirPurpose::class,
                'choice_label' => 'text',
                'label' => 'Цільове призначення',
                'placeholder' => 'вкажіть цільове призначення',
                'attr' => ['class' => 'form-control']
            ])
            ->add('address', TextType::class, [
                'label' => 'Адреса',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('isElectricity', CheckboxType::class, [
                'label' => 'Електрика',
                'attr' => ['class' => ''],
                'required' => false,
                'label_attr' => ['class' => 'font-weight-bold'],

            ])
            ->add('isWaterSupply', CheckboxType::class, [
                'label' => 'Водозабезпечення',
                'attr' => ['class' => ''],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('isRoad', CheckboxType::class, [
                'label' => 'Дорога з твердим покриттям',
                'attr' => ['class' => ''],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('isSewerage', CheckboxType::class, [
                'label' => 'Каналізація',
                'attr' => ['class' => ''],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('isGas', CheckboxType::class, [
                'label' => 'Газ',
                'attr' => ['class' => ''],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('geom', HiddenType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('photos', CollectionType::class, [
              //  'label' => 'Виберіть фото',
                'entry_type'   		=> PhotosType::class,
                'prototype'			=> true,
                'allow_add'			=> true,
                'allow_delete'		=> true,
                'by_reference' 		=> false,
                'required'			=> false,
                'label'			=> false,
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Advertisement::class,
            )
        );
        $resolver->setRequired('entity_manager');
    }


}