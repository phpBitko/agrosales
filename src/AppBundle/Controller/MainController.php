<?php
/**
 * Created by PhpStorm.
 * User: Vitalii
 * Date: 29.11.2018
 * Time: 17:13
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MainController
 * @Route("/main")
 */
class MainController extends Controller
{


    /**
     * @param Request $request
     * @Route("/",name="main_index", methods={"GET"})
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advertisement = $em->getRepository('AppBundle:Advertisement')->findAllWithLimit(8);
        if ($advertisement === null) {
            throw new NotFoundHttpException();
        }

        return $this->render('AppBundle:main:index.html.twig', array('advertisement' => $advertisement));

    }


}