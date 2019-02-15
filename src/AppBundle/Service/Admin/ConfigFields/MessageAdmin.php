<?php
/**
 * Created by PhpStorm.
 * User: bitko
 * Date: 13.02.2019
 * Time: 13:53
 */

namespace AppBundle\Service\Admin\ConfigFields;


use AppBundle\Entity\DirStatus;
use AppBundle\Entity\Messages;
use AppBundle\Service\Admin\AbstractAdmin;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Service\Admin\ListMapper;

class MessageAdmin extends AbstractAdmin
{
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('text', null, ['label' => 'Текст повідомлення'])
            ->add('addDate', null, ['label' => 'Дата додавання','attr' => ['class_column' =>'text-center']])
            ->add('isView', null, ['label' => 'Переглянуто', 'attr' => ['class_column' =>'text-center']])
            ->end($this->resolver)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => Messages::class,
            'main_menu_alias' => 'Таблиці',
            'sub_menu_alias' => 'Повідомлення',
            'order' => ['addDate' => 'desc']
        ));

    }


}