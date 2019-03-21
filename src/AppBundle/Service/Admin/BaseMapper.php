<?php

namespace AppBundle\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class BaseMapper
 * @package AppBundle\Service\Admin
 */
class BaseMapper
{
    /**
     * @var array
     */
    protected $listFieldName = [];

    /**
     * @var
     */
    protected $listFieldOptions;

    /**
     * @return array
     */
    public function getListFieldName()
    {
        return $this->listFieldName = array_keys($this->listFieldOptions);
    }


    /**
     * Повертає масив з ключами "main"(поля головної таблиці) і "foreign"(поля які звязані відношеннями з іншими таблицями)
     *
     * Example
     *
     * array:2 [▼
     *   "main" => array:3 [▼
     *      0 => "text"
     *      1 => "addDate"
     *      2 => "isView"
     *   ]
     *  "foreign" => array:1 [▼
     *      "users" => array:2 [▼
     *          0 => "username"
     *          1 => "email"
     *          ]
     *      ]
     *  ]
     *
     * @param $listField
     * @return array
     */
    public function preparationFieldForQuery($listField)
    {
        $field = [];
        foreach ($listField as $k => $v) {
            if (strpos($v, '.')) {
                $data = explode('.', $v);
                $field['foreign'][$data[0]][] = $data[1];
            } else {
                $field['main'][] = $v;
            }
        }
        return $field;
    }

    /**
     * @return mixed
     */
    public function getListFieldOptions()
    {
        return $this->listFieldOptions;
    }

}