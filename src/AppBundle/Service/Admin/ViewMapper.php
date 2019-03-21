<?php

namespace AppBundle\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Repository\UserRepository;

/**
 * Class ViewMapper
 * @package AppBundle\Service\Admin
 */
class ViewMapper extends BaseMapper
{

    /**
     * @param $name
     * @param array $fieldOption
     * @return $this
     */
    public function add($name, $fieldOption = [])
    {
        $fieldOption['name'] = $name;

        $this->listFieldOptions[$name] = $fieldOption;

        return $this;
    }
}