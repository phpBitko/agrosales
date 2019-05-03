<?php

namespace AppBundle\Service\Admin\ConfigFields;

use AppBundle\Entity\Messages;
use AppBundle\Entity\User;
use AppBundle\Service\Admin\AbstractAdmin;
use AppBundle\Service\Admin\FormMapper;
use AppBundle\Service\Admin\Interfaces\ListAdminInterface;
use AppBundle\Service\Admin\ListMapper;
use AppBundle\Service\Admin\ViewMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MessagesAdmin extends AbstractAdmin implements ListAdminInterface
{

    /**
     * @param ListMapper $listMapper
     * @return mixed|void
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('text', null, ['label' => 'Текст повідомлення'])
            ->add('addDate', null, ['label' => 'Дата додавання', 'attr' => ['class_column' => 'text-center']])
            ->add('isView', null, ['label' => 'Переглянуто', 'attr' => ['class_column' => 'text-center']])
            ->add('users.username', null, ['label' => 'Автор', 'attr' => ['class_column' => 'text-center']]);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('text', null, [
            'label' => 'Назва',
            'attr' => ['class' => 'form-control'],
            'required' => false
            ])
            ->add('isView', 	CheckboxType::class , [
                'label' => 'Переглянуто',
                'required' => false
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Автор',
                'attr' => ['class' => 'form-control']
            ]);
    }

    /**
     * @param ViewMapper $viewMapper
     */
    public function configureViewFields(ViewMapper $viewMapper)
    {
        $viewMapper
            ->add('text', ['label' => 'Текст повідомлення'])
            ->add('addDate', ['label' => 'Дата створення'])
            ->add('isView', ['label' => 'Чи переглянуто'])
            ->add('users.username', ['label' => 'Автор повідомлення']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => Messages::class,
            'main_menu_alias' => 'Таблиці',
            'sub_menu_alias' => 'Повідомлення',
            'order' => ['addDate' => 'desc'],
            'btn_action' => ['view', 'edit']
        ]);

    }


}