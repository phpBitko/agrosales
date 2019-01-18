<?php
namespace AppBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class AdminAdvertisementFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        $status = $em->getRepository('AppBundle:DirStatus')->findAll();

        $builder->add('dirStatus',  Filters\ChoiceFilterType::class, ['label' => 'Статус повідомлення',
            'attr' => ['class' => 'сol-3 form-control'],
            'label_attr' => ['class' => 'font-weight-bold'],
            'choices' => $status,
            'choice_label' => 'name',
            'placeholder' => 'Вкажіть статус',
            'required' => false]);
    }

    public function getBlockPrefix()
    {
        return 'admin_advertisement_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
        $resolver->setRequired('entity_manager');
    }
}