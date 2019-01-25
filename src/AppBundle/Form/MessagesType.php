<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 17.08.2018
 * Time: 12:55
 */

namespace AppBundle\Form;

use AppBundle\Entity\Messages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class MessagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('text', TextareaType::class, [
                'attr' => ['class' => 'form-control','placeholder' => 'Причина повернення...'],
                'label_attr' => ['class' => 'font-weight-bold'],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Messages::class,
            )
        );
    }


}