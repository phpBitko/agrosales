<?php
/**
 * Created by PhpStorm.
 * User: Vitalii
 * Date: 01.03.2019
 * Time: 14:23
 */

namespace AppBundle\Form\Helpers;

use Symfony\Component\Form\Extension\Core\Type\FormType;

class FormErrorHelper
{

    /**
     * @param $form
     * @param bool $param
     * @return array
     */
    public static function getErrorMessages($form, $param = false)
    {
        $errors = array();

        if ($param === false) {
            foreach ($form->getErrors() as $key => $error) {
                $errors[] = $error->getMessage();
            }

            foreach ($form->all() as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = self::getErrorMessages($child);
                }
            }

        } else {
            foreach ($form->getErrors(true) as $key => $error) {
                $strResult = '';
                $strKey = $error->getCause()->getPropertyPath();
                $strKey = explode('children', $strKey);
                foreach ($strKey as $key => $value) {
                    if ($value) {
                        $res = explode('.', $value, 2);
                        $strResult .= trim($res[0], "[,]") . '.';
                    }
                }
                $errors[rtrim($strResult, '.')] = $error->getMessage();
            }
        }
        return $errors;
    }
}