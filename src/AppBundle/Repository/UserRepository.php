<?php

namespace AppBundle\Repository;

use AppBundle\Repository\MyTrait\AdminTrait;
use Doctrine\ORM\EntityRepository;
use AppBundle\Repository\MyTrait\GeometryTrait;

/**
 * advertisementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    use AdminTrait;

}