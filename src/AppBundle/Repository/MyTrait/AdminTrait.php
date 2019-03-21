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
     * @param array $order
     *
     * @return array
     */
    public function findAllByFieldQuery(array $field, array $order = ['id'=>'ASC'])
    {
        $alias = 'a';
        foreach ($field['main'] as &$v) {
            $v = "$alias." . $v;
        }

        if(isset($field['foreign'])){
            foreach ($field['foreign'] as $k => &$val){

                array_walk($val, function (&$item, $key, $prefix){
                    $item = $prefix.'.'.$item. " as ".$prefix.'_'.$item;
                }, $k);

                $field['main'] = array_merge($field['main'], $val);
            }
        }

        $qb = $this->createQueryBuilder($alias)
            ->select($alias . '.id,' . implode(',', $field['main']));

        if(isset($field['foreign'])){
            foreach ($field['foreign'] as $k => $val){
                $qb = $qb->leftJoin($alias.'.'.$k, $k);
            }
        }

        $qb = $qb->orderBy($alias.'.'.key($order), $order[key($order)])
            ->getQuery();

        return $qb;
    }

    /**
     * Повертає масив по id, з визначеними полями
     *
     * @param int $id
     * @param array $field
     * @return mixed
     */
    public function findByIdOnlyCustomColumn($id, array $field = [])
    {
        $alias = 'a';
        foreach ($field['main'] as &$v) {
            $v = "$alias." . $v;
        }

        if(isset($field['foreign'])){
            foreach ($field['foreign'] as $k => &$val){

                array_walk($val, function (&$item, $key, $prefix){
                    $item = $prefix.'.'.$item. " as ".$prefix.'_'.$item;
                }, $k);

                $field['main'] = array_merge($field['main'], $val);
            }
        }

        $qb = $this->createQueryBuilder('a');
        if (!empty($field)) {
            $qb->select('a.id, '. implode(',', $field['main']));
        }

        if(isset($field['foreign'])){
            foreach ($field['foreign'] as $k => $val){
                $qb = $qb->leftJoin('a'.'.'.$k, $k);
            }
        }

        $qb->where('a.id = :ID')
            ->setParameter('ID', $id);

        $res = $qb->getQuery()->getResult();
        return $res;
    }

}