<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 17.08.2018
 * Time: 12:55
 */
namespace AppBundle\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class AddAdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('text_head',TextType::class)
           ->add('text_about',TextareaType::class)
           ->add('save',SubmitType::class);

    }

}