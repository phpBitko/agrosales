<?php

namespace AppBundle\Repository\MyTrait;


/**
 * Trait GeometryTrait
 * @package GromadaBundle\Repository\General
 */
trait AdminTrait
{
    /**
     * Вибирає всі об'єкти з визначеними полями
     *
     * @param array $field field by not null
     *
     * @return array
     */
    public function findAllByFieldQuery(array $field, array $order)
    {
        $alias = 'a';
        foreach ($field as &$v) {
            $v = "$alias." . $v;
        }

        dump(key($order));
        $qb = $this->createQueryBuilder($alias)
            ->select($alias . '.id,' . implode(',', $field))
            ->orderBy($alias.'.'.key($order), $order[key($order)])
            ->getQuery()
            ;

        return $qb;
    }

}