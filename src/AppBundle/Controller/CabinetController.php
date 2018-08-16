<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 15.08.2018
 * Time: 17:19
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * Class CabinetController
 * @Route('/cabinet')
 */

class CabinetController extends Controller
{
    /**
     * @Route('/', name=cabinet_index, methods={"GET"})
     */
    public function indexAction(Request $request)
    {

    }

}