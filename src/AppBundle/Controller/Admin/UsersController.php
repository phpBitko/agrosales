<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 15.08.2018
 * Time: 17:19
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Exception\WarningException;
use AppBundle\Service\PaginatorServices;
use Symfony\Component\EventDispatcher\Tests\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\AdvertisementType;
use AppBundle\Entity\Advertisement;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\Geometry;
use AppBundle\Filter\AdminAdvertisementFilterType;
use AppBundle\Controller\SuperController;


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
    public function getUsers()
    {


        return $this->render('AppBundle:admin/users:get_users.html.twig');


    }

}