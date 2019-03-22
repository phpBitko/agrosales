<?php

namespace AppBundle\Repository\MyTrait;
use Doctrine\ORM\QueryBuilder;


/**
 * Trait GeometryTrait
 * @package GromadaBundle\Repository\General
 */
trait GeometryTrait
{
    /**
     * Перевіряє валідність полігону
     *
     * @param $geom
     *
     * @return boolean
     */
    public function isValid($geom)
    {
        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare('select ST_IsValid(\'' . $geom . '\') = true as is_valid');
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result[0]['is_valid'];
    }

    /**
     * Рахує площу полігону в системі координат 4284
     *
     * @param $geom
     *
     * @return mixed
     */
    public function calcArea4284($geom)
    {
        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare('select ST_Area(ST_Transform(ST_Transform(ST_SetSRID(ST_GeomFromText(\'' . $geom . '\'),3857),4326),4284),true) as area');
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result[0]['area'];
    }


    /**
     * Додає до існуючого запиту QueryBuilder параметри фільтрації по колу або полігону
     *
     * @param QueryBuilder $qb
     * @param $geom
     * @param int $radius
     * @return QueryBuilder
     */
    public function qbAddGeom(QueryBuilder $qb, $geom, $radius = 0)
    {
        if ($radius) {
            $qb->andWhere('ST_Intersects(ST_Buffer(:GEOM,  :RADIUS), a.geom) = true')
                ->setParameter('GEOM', $geom)
                ->setParameter('RADIUS', $radius);
        } else {
            $qb->andWhere('ST_Intersects(ST_GeomFromText(:GEOM), a.geom) = true')
                ->setParameter('GEOM', $geom);
        }
        return $qb;
    }


    /**
     * Рахує площу полігону в будь якій системі координат
     *
     * @param $geom
     * @param string $sridIn Вихідна система координат
     * @param array $sridOut Системи коорддинат (ланцюжок перетворень)
     * @param boolean $useSpheroid Використання сфероїда
     * @return mixed
     * @throws \Exception
     */
    public function calcArea($geom, string $sridIn, array $sridOut = [], bool $useSpheroid = true)
    {
        $transform = preg_replace('/^(.*)$/', 'st_transform', $sridOut);
        if (empty($sridIn)) {
            throw new \Exception('Вхідна система координат не може бути пустою.');
        }
        $delimeter1 = empty($sridOut) ? '' : '(';
        $delimeter2 = empty($sridOut) ? '' : '),';
        $useSpheroid = $useSpheroid ? ',true' : '';
        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare('select ST_Area' . $delimeter1 . implode('(', $transform) .
                '(ST_SetSRID(\'' . $geom . '\'::geometry,' . $sridIn . $delimeter2
                . implode('),', $sridOut) . ')' . $useSpheroid . ') as area');
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result[0]['area'];
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
            ->where('ST_Within(ST_GeomFromText(:POLYGON,3857), st_transform(d.geometriesId.geom,3857)) = true')
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


    /**
     * Повертає всі об'єкти із зазначеними полями
     *
     * @param array $column
     * @return mixed
     */
    public function findAllOnlyCustomColumn($column = [], $order = [])
    {
        foreach ($column as &$val) {
            $val = 'a.' . $val;
        }
        $qb = $this->createQueryBuilder('a');
        if (!empty($column)) {
            $qb->select(implode(',', $column));
        }
        if (!empty($order) && isset($order['column']) && isset($order['order'])) {
            $qb->orderBy('a.' . $order['column'], $order['order']);
        }


        $res = $qb->getQuery()->getResult();
        return $res;
    }

    /**
     * Повертає extent зазначених об'єктів
     */
    public function getExtentById($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $sql = 'SELECT ST_XMin(ST_Extent(geom)), ST_YMin(ST_Extent(geom)),
                ST_XMax(ST_Extent(geom)), ST_YMax(ST_Extent(geom))
                FROM ' . $this->getEntityManager()->getClassMetadata($this->_entityName)->getTableName() . ' where id=' . $id;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return array_values($result[0]);
    }


    /**
     * Повертає об'єкт який перетинає точку, або false якщо не перетинає
     *
     * @param $pointGeom
     * @return bool
     */
    public function getPositionByGeom($pointGeom)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('ST_Intersects(a.theGeom900913 , ST_GeomFromText(:GEOM,900913)) = true')
            ->setParameter('GEOM', $pointGeom)
            ->getQuery();

        $id = $qb->getResult();
        if (!empty($id)) {
            return $id[0];
        }

        return;
    }
}