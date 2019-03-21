<?php
/**
 * Created by PhpStorm.
 * User: bitko
 * Date: 13.02.2019
 * Time: 13:53
 */

namespace AppBundle\Service\Admin\ConfigFields;


use AppBundle\Entity\DirStatus;
use AppBundle\Service\Admin\AbstractAdmin;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Service\Admin\ListMapper;
use AppBundle\Service\Admin\Interfaces\ListAdminInterface;
use AppBundle\Service\Admin\ViewMapper;

class DirStatusAdmin extends AbstractAdmin implements ListAdminInterface
{

    /**
     * @param ListMapper $listMapper
     * @return mixed|void
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, ['label' => 'Назва']);
        ;
    }

    /**
     * @param ViewMapper $viewMapper
     */
    public function configureViewFields(ViewMapper $viewMapper)
    {
        $viewMapper
            ->add('name')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => DirStatus::class,
            'main_menu_alias' => 'Таблиці',
            'sub_menu_alias' => 'Статуси оголошень',
            'btn_action' => ['view'],
            'validation_groups' => ['Default', 'Registration']
        ));

    }


}