<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 17.08.2018
 * Time: 12:55
 */

namespace AppBundle\Form;

use AppBundle\Entity\Advertisement;
//use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class AdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();

        $builder
            ->add('textHead', TextType::class, [
                'label' => 'Назва*',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('textAbout', TextareaType::class, [
                'label' => 'Опис',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Вартість',
                'currency' => 'UAH',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
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
                'choices' => array('га' => '1', 'соток' => '2', 'м2' => '3'),
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('dirPurpose', ChoiceType::class, [
                'label' => 'Цільове призначення',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'choices' => $purpose,
                'choice_label' => 'text',
                'placeholder' => 'вкажіть цільове призначення',
                'required' => false
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
//            ->add('imageFile', VichImageType::class, [
//                'label' => 'файл..',
//
//            ])
            ->add('photos', FileType::class, [
                'label' => 'Виберіть фото',
                'attr' => ['accept' => 'image/*'],
                'label_attr' => ['class' => 'font-weight-bold '],
                'multiple' => true,
                'required' => false

            ])
            ->add('coordB', NumberType::class, [
                'label' => 'Широта',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold '],
                'required' => false
            ])
            ->add('coordL', NumberType::class, [
                'label' => 'Довгота',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold '],
                'required' => false
            ])


//            ->add('photos', FileType::class, [
//                'label' => 'Виберіть фото',
//                'attr' => [
//                    'accept' => 'image/*,image/jpeg',
//                ],
//                'multiple'=> true,
//                'data_class'=>null,
//                'label_attr' => ['class' => 'font-weight-bold ']
//            ])
//
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