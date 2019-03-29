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
use AppBundle\Service\Admin\ConfigFields\UserAdmin;

/**
 * Class CabinetController
 * @Route("/admin/user")
 */
class UsersController extends BaseAdminController
{

    /**
     * @Route("/list", name="admin.users_list", methods={"GET"})
     *
     */
    public function listUsersAction(Request $request, UserAdmin $userAdmin, PaginatorServices $paginator)
    {
        return $this->listAction($request, $userAdmin, $paginator);
    }

}