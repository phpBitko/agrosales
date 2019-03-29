<?php

namespace AppBundle\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Repository\UserRepository;


/**
 * Class ListMapper
 * @package AppBundle\Service\Admin
 */
class ListMapper extends BaseMapper
{

    /**
     * @param $name
     * @param null $type
     * @param array $fieldOption
     * @return $this
     */
    public function add($name, $type = null, $fieldOption = [])
    {
        $fieldOption['name'] = $name;

        $this->listFieldOptions[$name] = $fieldOption;

        return $this;
    }




}