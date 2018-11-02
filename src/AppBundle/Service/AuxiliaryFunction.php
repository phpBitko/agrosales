<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;


class AuxiliaryFunction
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
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }


    private function sortByIdOkrug($a, $b)
    {
        $a = $a['idOkrug'];
        $b = $b['idOkrug'];

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    private function sortByIdProvince($a, $b)
    {
        $a = $a['idProvince'];
        $b = $b['idProvince'];

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }



}