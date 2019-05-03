<?php

namespace AppBundle\Service;

use AppBundle\Exception\ClientException;
use AppBundle\Entity\DirDistrict;
use AppBundle\Entity\DirRegion;


class Geometry extends BaseEmServices
{
    /**
     * @param $geom
     * @return mixed
     * @throws \Exception
     */
    public function getPositionAddress($geom)
    {
        $region = $this->getPositionRegion($geom);

        if ($region === null) {
            throw new ClientException('Область не знайдено!');
        }

        $data['region'] = $region->getNatoobl();

        $district = $this->getPositionDistrict($geom);

        if ($district === null) {
            throw new ClientException('Район не знайдено!');
        }

        $data['district'] = $district->getNatoray();

        return $data;
    }

    /**
     * @param $geom
     * @return DirRegion|null
     */
    public function getPositionRegion($geom): ?DirRegion
    {

        $region = $this->entityManager->getRepository('AppBundle:DirRegion')->getPositionByGeom($geom);

        return $region;
    }


    /**
     * @param $geom
     * @return DirDistrict|null
     */
    public function getPositionDistrict($geom): ?DirDistrict
    {

        $district = $this->entityManager->getRepository('AppBundle:DirDistrict')->getPositionByGeom($geom);

        return $district;
    }
}