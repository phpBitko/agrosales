<?php

namespace AppBundle\Service\Admin;

use Symfony\Component\OptionsResolver\OptionsResolver;



abstract class AbstractAdmin
{
    protected $listMapper;

    protected $resolver;

    public function __construct(ListMapper $listMapper)
    {
        $this->resolver = new OptionsResolver();
        $this->configureOptions($this->resolver);
        $this->listMapper = $listMapper;
    }

    /**
     * @required
     *
     */
    public abstract function configureListFields(ListMapper $listMapper);

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'main_menu_alias' => 'Розділ',
            'sub_menu_alias' => 'Підрозділ',
            'num_items_by_page' => 20,
            'order' => ['id' => 'ASC']
        ));

    }


    public function renderView(){

    }


    /**
     * @return OptionsResolver
     */
    public function getResolver(): OptionsResolver
    {
        return $this->resolver;
    }

    public function getListFieldOptions(): array
    {
        return $this->listMapper->getListFieldOptions();
    }

    public function getResultSelectionQuery()
    {
        return $this->listMapper->getSelectionQuery();
    }

}


