<?php

namespace AppBundle\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractAdmin
{
    /**
     * @var
     */
    protected $listMapper;

    /**
     * @var
     */
    protected $viewMapper;

    /**
     * @var OptionsResolver
     */
    protected $resolver;

    /**
     * @var
     */
    protected $listQuery;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AbstractAdmin constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->resolver = new OptionsResolver();
        $this->configureOptions($this->resolver);

        $this->initialize();
    }

    /**
     *
     */
    public function initialize()
    {
        $listMapper = new ListMapper();
        $this->configureListFields($listMapper);

        $listField = $listMapper->getListFieldName();

        $listField = $listMapper->preparationFieldForQuery($listField);

        $options = $this->getOptions();
        $targetRepository = $this->em->getRepository($options['data_class']);

        $this->listQuery = $targetRepository->findAllByFieldQuery($listField, $options['order']);

        $this->listMapper = $listMapper;
    }

    /**
     * @return mixed
     */
    public function getListQuery()
    {
        return $this->listQuery;
    }

    /**
     * @param \AppBundle\Service\Admin\ListMapper $listMapper
     * @return mixed
     */
    public abstract function configureListFields(ListMapper $listMapper);

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'main_menu_alias' => 'Розділ',
            'sub_menu_alias' => 'Підрозділ',
            'num_items_by_page' => 20,
            'order' => ['id' => 'ASC'],
        ));
    }

    /**
     * @return OptionsResolver
     */
    public function getResolver(): OptionsResolver
    {
        return $this->resolver;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->resolver->resolve();
    }

    /**
     * @return \AppBundle\Service\Admin\ListMapper
     */
    public function getListMapper(): ListMapper
    {
        return $this->listMapper;
    }

    /**
     * @return \AppBundle\Service\Admin\ViewMapper
     */
    public function getViewMapper(): ViewMapper
    {
        return $this->viewMapper;
    }

}
