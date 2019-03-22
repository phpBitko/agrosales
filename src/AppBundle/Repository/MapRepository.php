<?php
/**
 * Created by PhpStorm.
 * User: Vitalii
 * Date: 13.03.2019
 * Time: 14:45
 */

namespace AppBundle\Repository;


use AppBundle\Repository\MyTrait\GeometryTrait;
use Doctrine\ORM\EntityRepository;

class MapRepository extends EntityRepository
{
    use GeometryTrait;

}