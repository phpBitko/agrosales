<?php

namespace AppBundle\Service;

use AppBundle\Exception\WarningException;
use AppBundle\Entity\DirDistrict;
use AppBundle\Entity\DirRegion;
use Doctrine\ORM\EntityManagerInterface;


class Geometry
{

    /**
     * @var string
     */
    protected $errors;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;


    /**
     * Currency constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param $pointGeom
     * @return mixed
     * @throws \Exception
     */
    public function getPositionAddress($pointGeom){

        $region = $this->getPositionRegion($pointGeom);
        $data['region'] = $region->getNatoobl();

        $district = $this->getPositionDistrict($pointGeom);
        $data['district'] = $district->getNatoray();

        return $data;
    }


    /**
     * @param $pointGeom
     * @return DirRegion
     * @throws \Exception
     */
    public function getPositionRegion($pointGeom): DirRegion  {
        $region = $this->entityManager->getRepository('AppBundle:DirRegion')->getPositionByGeom($pointGeom);

        if ($region === false) {
            throw new WarningException('Область не знайдено!');
        }

        return $region;
    }

    /**
     * @param $pointGeom
     * @return DirDistrict
     * @throws \Exception
     */
    public function getPositionDistrict($pointGeom): DirDistrict  {
        $district = $this->entityManager->getRepository('AppBundle:DirDistrict')->getPositionByGeom($pointGeom);

        if ($district === false) {
            throw new WarningException('Район не знайдено!');
        }

        return $district;
    }


    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }


}