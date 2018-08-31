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


class AdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];;
        $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();

        $builder
            ->add('textHead', TextType::class, [
                'label' => 'Назва',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold']
            ])
            ->add('textAbout', TextareaType::class, [
                'label' => 'Опис',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold']
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Вартість',
                'currency' => 'UAH',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold']
            ])
            ->add('declarantPhoneNum', TelType::class, [
                'label' => 'Телефон',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold']
            ])
            ->add('area', NumberType::class, [
                'label' => 'Площа',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold']
            ])
            ->add('areaUnit', ChoiceType::class, [
                'label' => 'Одиниця площі',
                'choices' => array('га' => '1', 'соток' => '2', 'м2' => '3'),
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold']
            ])
            ->add('dirPurpose', ChoiceType::class, [
                'label' => 'Цільове призначення',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'choices' => $purpose,
                'choice_label' => 'text',
                'placeholder' => 'вкажіть цільове призначення'
            ])
            ->add('address', TextType::class, [
                'label' => 'Адреса',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold']
            ])
            ->add('isElectricity', CheckboxType::class, [
                'label' => 'Електрика',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'label_attr' => ['class' => 'font-weight-bold'],

            ])
            ->add('isWaterSupply', CheckboxType::class, [
                'label' => 'Водозабезпечення',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('isRoad', CheckboxType::class, [
                'label' => 'Дорога з твердим покриттям',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('isSewerage', CheckboxType::class, [
                'label' => 'Каналізація',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('isGas', CheckboxType::class, [
                'label' => 'Газ',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ])
            ->add('photos', FileType::class, [
                'label' => 'Виберіть фото',
                'attr' => [
                    'class' => 'fas fa-cloud-upload-alt',
                    'accept' => 'image/*,image/jpeg',
                    'multiple'=> true
                ],
                'label_attr' => ['class' => 'font-weight-bold '],
                'data_class' => null

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