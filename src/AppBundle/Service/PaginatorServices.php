<?php

namespace AppBundle\Service;

use Knp\Component\Pager\PaginatorInterface;
use AppBundle\Entity\Advertisement;
use Doctrine\ORM\EntityManagerInterface;

class PaginatorServices  extends BaseEmServices
{

    private $paginator;

    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        parent::__construct($entityManager);
        $this->paginator = $paginator;
    }


    public function getPagination($query, $page, $numItems =  Advertisement::NUM_ITEMS){

        return $this->paginator->paginate($query, $page, $numItems);
    }

}