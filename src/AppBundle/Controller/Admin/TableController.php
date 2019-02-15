<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Service\PaginatorServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Controller\SuperController;
use AppBundle\Service\Admin\ConfigFields\DirStatusAdmin;

/**
 * Class CabinetController
 * @Route("/admin/table")
 */
class TableController extends SuperController
{

    /**
     * @Route("/getStatus", name="admin.table_get_status", methods={"GET"})
     *
     */
    public function getUsers(Request $request, DirStatusAdmin $dirStatusAdmin, PaginatorServices $paginator)
    {
        //Отримуєм квері для пагінатора
        $query = $dirStatusAdmin->getResultSelectionQuery();

        //Отримуємо загальні налаштування для пагінатора, і рендера сторінки
        $options = $dirStatusAdmin->getResolver()->resolve();

        $pagination = $paginator->getPagination($query, $request->query->getInt('page', 1), $options['num_items_by_page']);

        return $this->render('AppBundle:admin/template:body_template.html.twig',
            [
                'options' => $dirStatusAdmin->getResolver()->resolve(),
                'fieldOptions' => $dirStatusAdmin->getListFieldOptions(),
                'data' => $pagination
            ]);
    }


}