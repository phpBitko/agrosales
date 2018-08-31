<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Advertisement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AdvertisementController extends Controller
{
    /**
     * @Route("/",  defaults={"page": 1}, name="advertisement_index", methods={"GET"})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="advertisement_index_paginated")
     *
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        //$advertisement = $em->getRepository('AppBundle:Advertisement')->findAll( );
        //$advertisement2 = $em->getRepository('AppBundle:Advertisement')->findFirstTen();
        $advertisement3 = $em->getRepository('AppBundle:Advertisement')->findLatest($page);
        dump($advertisement3);
        // replace this example code with whatever you need
        return $this->render('AppBundle:advertisement:index.html.twig', array('advertisement' => $advertisement3));
    }
}
