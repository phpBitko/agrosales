<?php

namespace AppBundle\Service\Admin\ConfigFields;

use AppBundle\Entity\User;
use AppBundle\Service\Admin\AbstractAdmin;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Service\Admin\ListMapper;


class UsersAdmin extends AbstractAdmin
{
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('username', null, ['label' => 'Логін', 'attr'=>['class_head'=>'w-25', 'class_column' =>'text-center']])
            ->add( 'email', null, ['label' => 'Електронна адреса', 'attr'=>['class'=>'w-30','class_column' =>'text-left']])
            ->add('enabled', null, ['label' => 'Активність', 'attr'=>['class'=>'w-15', 'class_column' =>'text-center']])
            ->add('roles', null, ['label' => 'Роль', 'attr'=>['class'=>'w-25', 'class_column' =>'text-center']])
            ->end($this->resolver)
        ;
    }  

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => User::class,
            'main_menu_alias' => 'Користувачі',
            'sub_menu_alias' => 'Список користувачів',
        ));

    }

}


