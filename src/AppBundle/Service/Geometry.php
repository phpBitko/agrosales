<?php

namespace AppBundle\Service;

use AppBundle\Exception\WarningException;
use AppBundle\Entity\DirDistrict;
use AppBundle\Entity\DirRegion;

class Geometry extends BaseEmServices
{

    /**
     * @param $pointGeom
     * @return mixed
     * @throws \Exception
     */
    public function getPositionAddress($pointGeom){

        $region = $this->getPositionRegion($pointGeom);

        if ($region === null) {
            throw new WarningException('Область не знайдено!');
        }

        $data['region'] = $region->getNatoobl();

        $district = $this->getPositionDistrict($pointGeom);

        if ($district === null) {
            throw new WarningException('Район не знайдено!');
        }

        $data['district'] = $district->getNatoray();

        return $data;
    }


    /**
     * @param $pointGeom
     * @return DirRegion|null
     */
    public function getPositionRegion($pointGeom): ?DirRegion  {

        $region = $this->entityManager->getRepository('AppBundle:DirRegion')->getPositionByGeom($pointGeom);

        return $region;
    }


    /**
     * @param $pointGeom
     * @return DirDistrict|null
     */
    public function getPositionDistrict($pointGeom): ?DirDistrict  {

        $district = $this->entityManager->getRepository('AppBundle:DirDistrict')->getPositionByGeom($pointGeom);

        return $district;
    }
}