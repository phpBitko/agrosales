<?php

namespace AppBundle\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Repository\UserRepository;

class ListMapper
{
    /**
     * @var array
     */
    private $listFieldName = [];

    private $listFieldOptions;

    private $selectionQuery;


    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function add($name, $type = null, $fieldOption = [])
    {

        $fieldOption['name'] = $name;

        $this->listFieldOptions[$name] = $fieldOption;
        return $this;
    }


    private function getListFieldName($list)
    {
        if (is_array($list)) {
            return $this->listFieldName = array_keys($list);
        }

        throw new \InvalidArgumentException();
    }


    public function end(OptionsResolver $resolver)
    {
        $options = $resolver->resolve();
        $listField = $this->getListFieldName($this->listFieldOptions);

        $targetRepository = $this->em->getRepository($options['data_class']);
        $this->selectionQuery = $targetRepository->findAllByFieldQuery($listField, $options['order']);

    }

    /**
     * @return mixed
     */
    public function getListFieldOptions()
    {
        return $this->listFieldOptions;
    }

    /**
     * @return mixed
     */
    public function getSelectionQuery()
    {
        return $this->selectionQuery;
    }

}