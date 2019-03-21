<?php

namespace AppBundle\Service\Admin\ConfigFields;

use AppBundle\Entity\User;
use AppBundle\Service\Admin\AbstractAdmin;
use AppBundle\Service\Admin\Interfaces\ListAdminInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Service\Admin\ListMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Service\Admin\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserAdmin extends AbstractAdmin implements ListAdminInterface
{

    /**
     * @var array
     */
    private $roleHierarchy;


    /**
     * UserAdmin constructor.
     * @param EntityManagerInterface $em
     * @param array $roleHierarchy
     */
    public function __construct(EntityManagerInterface $em, array $roleHierarchy){
        $this->roleHierarchy = $roleHierarchy;
        parent::__construct($em);
    }

    /**
     * @param ListMapper $listMapper
     * @return mixed|void
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('username', null, ['label' => 'Логін', 'attr'=>['class_head'=>'w-25', 'class_column' =>'text-center']])
            ->add( 'email', null, ['label' => 'Електронна адреса', 'attr'=>['class'=>'w-30','class_column' =>'text-left']])
            ->add('enabled', null, ['label' => 'Активність', 'attr'=>['class'=>'w-15', 'class_column' =>'text-center']])
            ->add('roles', null, ['label' => 'Роль', 'attr'=>['class'=>'w-25', 'class_column' =>'text-center']])
        ;
    }


    /**
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $roles = array_combine(array_keys($this->roleHierarchy),array_keys($this->roleHierarchy));

        $formMapper->add('username', null, ['label' => 'Логін', 'attr' => ['class' => 'form-control']])
            ->add( 'email', null, ['label' => 'Електронна адреса', 'attr' => ['class' => 'form-control']])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Пароль', 'attr' => ['class' => 'form-control']),
                'second_options' => array('label' => 'Повторити пароль',  'attr' => ['class' => 'form-control']),
                'label' => false,
                'required' => false
            ))
            ->add('roles',  ChoiceType::class, [
                'choices'=>$roles,
                'attr' => ['class' => 'form-control'],
                'label' => 'Ролі користувачів',
                'multiple'=>true,
            ])
            ->add('enabled', CheckboxType::class, [
            'label' => 'Активність',
            'required' => false
        ]);
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => User::class,
            'main_menu_alias' => 'Користувачі',
            'sub_menu_alias' => 'Список користувачів',
            'order' => ['username' => 'asc'],
            'btn_action' => ['edit']
        ));
    }
}


