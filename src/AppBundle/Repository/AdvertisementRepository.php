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

//Добавити DOC блоки
class AdvertisementRepository extends EntityRepository
{

    /**
     * Вибирає всі активні об'єкти де вказане поле поле $param не Null
     *
     * @param string $field field by not null
     * @param int $all 1 - all fields, 0 - id,geom fields
     * @param int $status table dir_status, default = active
     *
     * @return array
     */
    public function findAllByNotNull($field, $all = 1, $status = 1)
    {
        $qb = $this->createQueryBuilder('a');

        if ($all == 1) {
            $qb->select('a')->where($qb->expr()->isNotNull('a.' . $field));
        } else {
            $qb->select('a.id', 'a.geom')->where($qb->expr()->isNotNull('a.' . $field));
        }

        $qb->andWhere('a.dirStatus = :STATUS')
            ->setParameter('STATUS', $status);

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }

    /**
     * Вибірає всі обєкти з параметрами (максимальна кількість записів, поле по якому сортуєм, статус)
     *
     * @param int $limit
     * @param array $order
     * @param int $status table dir_status, default = all
     *
     * @return mixed
     */
    public function findAllWithLimit($limit = null, $status = null, $order = ['addDate' => 'DESC'])
    {
        $qb = $this->createQueryBuilder('q');

        if (!empty($status)) {
            $qb->andWhere('q.dirStatus = :STATUS')
                ->setParameter('STATUS', $status);
        }

        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }

        foreach ($order as $field => $sort){
            $qb->addOrderBy("q." . $field, $sort);
        }

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }


    public function queryLatest($status = null, $order = ['addDate' => 'DESC'])
    {
        $qb = $this->createQueryBuilder('q');
        if (!empty($status)) {
            $qb->andWhere("q.dirStatus = :STATUS")
                ->setParameter(':STATUS', $status);
        }

        $qb = $qb->addOrderBy("q." . key($order), $order[key($order)])
            ->getQuery();
        return $qb;
    }

    public function queryLatestFilter()
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.dirStatus = 1')
            ->orderBy('q.isTop', 'DESC')
            ->addOrderBy('q.addDate', 'DESC');

        return $qb;
    }


    public function findLatest($page = 1)
    {
        $adapter = new DoctrineORMAdapter($this->queryLatest(1));
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(Advertisement::NUM_ITEMS);
        $paginator->setCurrentPage($page);
        return $paginator;
    }

    public function findLatestFilter($query, $page = 1)
    {
        $adapter = new DoctrineORMAdapter($query);
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