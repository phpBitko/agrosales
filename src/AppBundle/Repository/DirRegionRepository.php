<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Repository\MyTrait\GeometryTrait;

/**
 * advertisementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DirRegionRepository extends EntityRepository
{
    use GeometryTrait;

}