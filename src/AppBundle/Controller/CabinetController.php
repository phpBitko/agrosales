<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 15.08.2018
 * Time: 13:08
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CabinetController
 * @package AppBundle\Controller
 * @Route('/cab')
 */
class CabinetController extends Controller
    /**
     * @Route('/')
     */
{
    public function indexAction(Request $request)
    {


    }

}