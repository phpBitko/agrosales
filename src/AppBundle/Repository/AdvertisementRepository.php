<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 13.08.2018
 * Time: 11:59
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Advertisement;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class AdvertisementRepository extends EntityRepository
{
    public function findFirstTen()
    {
        $qb=$this->createQueryBuilder('q')
            ->orderBy('q.addDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
            dump($qb);
        return $qb;

    }
    public function queryLatest()
    {
        $qb=$this->createQueryBuilder('q')
            ->orderBy('q.addDate', 'DESC')
            ->getQuery();
        dump($qb);

        return $qb;

    }
    public function findLatest($page=1)
{
    $adapter = new DoctrineORMAdapter($this->queryLatest());
    $paginator = new Pagerfanta($adapter);
    $paginator ->setMaxPerPage(Advertisement::NUM_ITEMS);
    $paginator->setCurrentPage($page);
    return $paginator;

}

}