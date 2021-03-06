<?php

namespace AppBundle\Service\Admin;

use AppBundle\Service\Admin\Interfaces\ListAdminInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @var ContainerInterface
     */
    protected $container;

    /**
     * AbstractAdmin constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->resolver = new OptionsResolver();
        $this->configureOptions($this->resolver);
    }

    /**
     * Тільки для класів які реалізують ListAdminInterface
     */
    public function initializeList()
    {
        if(!($this instanceof ListAdminInterface)){
            throw new \Exception('Об\'єкт не реалізує ListAdminInterface');
        }
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
     * @return ListMapper
     */
    public function getListMapper(): ListMapper
    {
        return $this->listMapper;
    }

    /**
     * @return ViewMapper
     */
    public function getViewMapper(): ViewMapper
    {
        return $this->viewMapper;
    }

}
