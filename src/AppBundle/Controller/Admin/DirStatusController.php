<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Service\PaginatorServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Controller\SuperController;
use AppBundle\Service\Admin\ConfigFields\DirStatusAdmin;

/**
 * Class CabinetController
 * @Route("/admin/dirStatus")
 */
class DirStatusController extends BaseAdminController
{

    /**
     * @Route("/list", name="admin.dirStatus_list", methods={"GET"})
     *
     */
    public function dirStatusListAction(Request $request, DirStatusAdmin $dirStatusAdmin, PaginatorServices $paginator)
    {
        return $this->listAction($request, $dirStatusAdmin, $paginator);
    }

}