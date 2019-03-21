<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Service\Admin\ConfigFields\MessagesAdmin;
use AppBundle\Service\PaginatorServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CabinetController
 * @Route("/admin/messages")
 */
class MessagesController extends BaseAdminController
{

    /**
     * @Route("/list", name="admin.messages_list", methods={"GET"})
     *
     */
    public function listMessagesAction(Request $request, MessagesAdmin $messagesAdmin, PaginatorServices $paginator)
    {
        return $this->listAction($request, $messagesAdmin, $paginator);
    }
}