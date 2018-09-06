<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 06.09.2018
 * Time: 14:21
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;


/**
 * Class MapController
 * @Route("/map")
 *
 */
class MapController extends Controller
{
    /**
     * @Route("/", name="map_index")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:map:index.html.twig');



    }
}