<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Service\Admin\ConfigFields\MessageAdmin;
use AppBundle\Service\PaginatorServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Controller\SuperController;

/**
 * Class CabinetController
 * @Route("/admin/table")
 */
class MessageController extends SuperController
{

    /**
     * @Route("/getMessage", name="admin.table_get_message", methods={"GET"})
     *
     */
    public function getUsers(Request $request, MessageAdmin $messageAdmin, PaginatorServices $paginator)
    {
        //Отримуєм квері для пагінатора
        $query = $messageAdmin->getResultSelectionQuery();

        //Отримуємо загальні налаштування для пагінатора, і рендера сторінки
        $options = $messageAdmin->getResolver()->resolve();

        $pagination = $paginator->getPagination($query, $request->query->getInt('page', 1), $options['num_items_by_page']);

        return $this->render('AppBundle:admin/template:body_template.html.twig',
            [
                'options' => $messageAdmin->getResolver()->resolve(),
                'fieldOptions' => $messageAdmin->getListFieldOptions(),
                'data' => $pagination
            ]);
    }


}