<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;


class BaseEmServices
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
}