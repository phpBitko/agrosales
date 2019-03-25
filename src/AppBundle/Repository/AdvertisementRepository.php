<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 13.08.2018
 * Time: 11:59
 */

namespace AppBundle\Repository;

use AppBundle\Repository\MyTrait\GeometryTrait;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

//Добавити DOC блоки
class AdvertisementRepository extends EntityRepository
{
    use GeometryTrait;

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
        $qb = $this->qbfindAllByNotNull($field, $all, $status);

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }

    /**
     * Вибирає всі активні об'єкти де вказане поле поле $param не Null
     *
     * @param string $field field by not null
     * @param int $all 1 - all fields, 0 - id,geom fields
     * @param int $status table dir_status, default = active
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function qbFindAllByNotNull($field, $all = 1, $status = 1)
    {
        $qb = $this->createQueryBuilder('a');

        if ($all == 1) {
            $qb->select('a')->where($qb->expr()->isNotNull('a.' . $field));
        } else {
            $qb->select('a.id', 'a.geom')->where($qb->expr()->isNotNull('a.' . $field));
        }

        $qb->andWhere('a.dirStatus = :STATUS')
            ->setParameter('STATUS', $status);

        return $qb;
    }


    /**
     * Вибірає всі обєкти з параметрами (максимальна кількість записів, поле по якому сортуєм, статус)
     *
     * @param int $limit
     * @param int $status table dir_status, default = all
     * @param array $order
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

        foreach ($order as $field => $sort) {
            $qb->addOrderBy("q." . $field, $sort);
        }

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }


    /**
     * Вибираэмо усі оголошення по статусам, для користувача. Якщо id користувача не передано, тоді вибираємо усі.
     *
     * @param $userId
     * @return mixed
     */
    public function getCountAdvertisementByStatus($userId = 0)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.dirStatus) as count, s.id, s.name')
            ->innerJoin('AppBundle:dirStatus', 's', 'with', 'a.dirStatus = s.id');

        if ($userId > 0) {
            $qb->where('a.idUser = :USER')
                ->setParameter('USER', $userId);
        }

        $res = $qb->groupBy('a.dirStatus, s.id, s.name')
            ->getQuery()
            ->getResult();

        return $res;
    }

    /**
     *
     * Підраховує загальну кількість переглядів
     *
     * @param $userId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalCountView($userId)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('sum(a.viewCount)')
            ->where('a.idUser = :USER')
            ->setParameter('USER', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }


    /**
     *
     * @param null $status
     * @param array $order
     * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
     */
    public function queryFindByStatus($status = null, $order = ['addDate' => 'DESC'], $user = null)
    {
        $qb = $this->qbFindByStatus($status, $order, $user);

        return $qb->getQuery();
    }


    /**
     * @param null $status
     * @param array $order
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function qbFindByStatus($status = null, $order = ['addDate' => 'DESC'], $user = null)
    {
        $qb = $this->createQueryBuilder('q');

        if (!empty($status)) {
            $qb->andWhere("q.dirStatus = :STATUS")
                ->setParameter(':STATUS', $status);
        }

        if (!empty($user)) {
            $qb->andWhere("q.idUser = :USER")
                ->setParameter(':USER', $user);
        }

        foreach ($order as $field => $sort) {
            $qb->addSelect("CASE WHEN q.$field IS NULL THEN 1 ELSE 0 END as HIDDEN " . $field . "_is_null");
            $qb->addOrderBy($field . "_is_null", 'ASC');
            $qb->addOrderBy("q." . $field, $sort);
        }
        return $qb;
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