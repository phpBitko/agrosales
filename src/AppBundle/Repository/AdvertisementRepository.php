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
/*        public function selectPoint()
        {
            $qb = $this->createQueryBuilder('q')
                ->select('q.id', 'q.geom')
                ->where('q.geom != :select')
                ->setParameter('select', 'null')
                ->getQuery()
                ->getResult();
            return $qb;
        }*/
//------------------------------- вибираємо оголошення які мають статус активні - (1)
    public function selectPoint()
    {
        $qb = $this->createQueryBuilder('q')
            ->select('q.id', 'q.geom')
            ->leftJoin('q.dirStatus', 'status')
            ->where('q.geom != :select')
            ->andWhere('status.id = 1')
            ->setParameter('select', 'null')
            ->getQuery()
            ->getResult();
        return $qb;
    }


    /*public function detailsAdvertisement($id) {
        $qb = $this->createQueryBuilder('q')
            ->select('q.price', 'q.area','q.isActive',
                'q.isGas','q.isRoad','q.isSewerage','q.isWaterSupply',
                'q.isElectricity','purpose.text as text', 'photo1.id')
            ->leftJoin('q.dirPurpose','purpose')
            ->leftJoin('q.photos','photo1')
            ->where('q.id = :ID')
            ->setParameter('ID', $id)
            ->getQuery()
            ->getResult();
        dump($qb);
        return $qb;
    }*/

    public function detailsAdvertisement2($id)
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.id = :ID')
            ->setParameter('ID', $id)
            ->getQuery()
            ->getResult();
        dump($qb);
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
        return $result;
    }

    /*    public function findFirstTen()
        {
            $qb = $this->createQueryBuilder('q')
                ->orderBy('q.addDate', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
            return $qb;

        }*/

    public function findLatestTitle()
    {
        $qb = $this->createQueryBuilder('q')
            ->orderBy('q.addDate', 'DESC')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function queryLatest()
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.dirStatus = 1')
            ->orderBy('q.isTop', 'DESC')
            ->addOrderBy('q.addDate', 'DESC')
            ->getQuery();
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