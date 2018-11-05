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
    public function selectPoint()
    {
        $qb = $this->createQueryBuilder('q')
            ->select('q.id', 'q.geom')
            ->where('q.geom != :select')
            ->setParameter('select', 'null')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    /**
     * @param int $param
     * @param int $all 1 - all fields, 0 - id,geom fields
     *
     * @return array
     */
    public function findByNotNull($param, $all = 1)
    {
        $qb = $this->createQueryBuilder('a');
        if ($all == 1) {
            $qb->select('a')->where($qb->expr()->isNotNull('a.' . $param));
        } else {
            $qb->select('a.id', 'a.geom')->where($qb->expr()->isNotNull('a.' . $param));
        }
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        dump($result);
        return $result;
    }

    public function findFirstTen()
    {
        $qb = $this->createQueryBuilder('q')
            ->orderBy('q.addDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        dump($qb);
        return $qb;

    }

    public function queryLatest()
    {
        $qb = $this->createQueryBuilder('q')
            ->orderBy('q.addDate', 'DESC')
            ->getQuery();
        dump($qb);

        return $qb;

    }

    public function findLatest($page = 1)
    {
        $adapter = new DoctrineORMAdapter($this->queryLatest());
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(Advertisement::NUM_ITEMS);
        $paginator->setCurrentPage($page);
        return $paginator;

    }
//    public function findPoints()
//    {
//        $qb=$this->createQueryBuilder('q')
//        ->select()
//    }


    /**
     * Перевіряє, чи знаходиться задана геометрія ($geom) повністю всередині полігону з переданим ID ($id)
     *
     * @param $geom
     * @param $id
     *
     * @return bool
     */
    public function ifPolygonWithin($geom, $id)
    {
        $qb = $this->createQueryBuilder('d')
            ->where('ST_Within(ST_GeomFromText(:POLYGON,3857), st_transform(d.geom,3857)) = true')
            ->andWhere('d.id = :ID')
            ->andWhere('ST_IsValid(:POLYGON) = true')
            ->setParameter(':POLYGON', $geom)
            ->setParameter(':ID', $id)
            ->getQuery()
            ->getResult();
        if (empty($qb)) {
            return false;
        }
        return true;
    }

}