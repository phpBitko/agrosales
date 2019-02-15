<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 15.08.2018
 * Time: 17:19
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Service\PaginatorServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Controller\SuperController;
use AppBundle\Service\Admin\ConfigFields\UsersAdmin;

/**
 * Class CabinetController
 * @Route("/admin/users")
 */
class UsersController extends SuperController
{


    /**
     * @Route("/getUsers", name="admin.users_get_users", methods={"GET"})
     *
     */
    public function getUsers(Request $request, UsersAdmin $usersAdmin, PaginatorServices $paginator)
    {
        //Отримуєм квері для пагінатора
        $query = $usersAdmin->getResultSelectionQuery();

        //Отримуємо загальні налаштування для пагінатора, і рендера сторінки
        $options = $usersAdmin->getResolver()->resolve();

        $pagination = $paginator->getPagination($query, $request->query->getInt('page', 1), $options['num_items_by_page']);

        return $this->render('AppBundle:admin/template:body_template.html.twig',
            [
                'options' => $usersAdmin->getResolver()->resolve(),
                'fieldOptions' => $usersAdmin->getListFieldOptions(),
                'data' => $pagination
            ]);
    }

    private function setRoleUser($data){

        foreach ($data as &$v){
            if(empty($v['roles'])){
                $v['roles'] = 'ROLE_USER';
            }
        }

        return $data;
    }


}