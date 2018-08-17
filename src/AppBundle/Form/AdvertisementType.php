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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('textHead',TextType::class)
           ->add('textAbout',TextareaType::class)
           ->add('save',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' =>  Advertisement::class,
            )
        );
    }

}