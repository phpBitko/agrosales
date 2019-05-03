<?php

namespace AppBundle\Service\Cabinet\ConfigFields;

use AppBundle\Entity\User;
use AppBundle\Service\Admin\AbstractAdmin;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Service\Admin\FormMapper;


class UserCabinet extends AbstractAdmin
{

    /**
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('username', null, ['label' => 'Логін', 'attr' => ['class' => 'form-control']])
            ->add( 'email', null, ['label' => 'Електронна адреса', 'attr' => ['class' => 'form-control']]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => User::class,
            'main_menu_alias' => 'Профіль користувача',
            'sub_menu_alias' => 'Користувач',
            'order' => ['username' => 'asc'],
            'btn_action' => ['edit']
        ));
    }
}


