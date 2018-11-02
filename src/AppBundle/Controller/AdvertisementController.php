<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Advertisement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


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

        $auxiliaryFunctionService = $this->get('app.service.auxiliary_function');
        dump($auxiliaryFunctionService);
        //$advertisement = $em->getRepository('AppBundle:Advertisement')->findAll( );
        //$advertisement2 = $em->getRepository('AppBundle:Advertisement')->findFirstTen();
        $advertisement3 = $em->getRepository('AppBundle:Advertisement')->findLatest($page);

        // replace this example code with whatever you need
        return $this->render('AppBundle:advertisement:index.html.twig', array('advertisement' => $advertisement3));
    }

    /**
     * @Route("/getAllAdvertisement", name="get_all_advertisement", options={"expose"=true}, methods={"POST"})
     *
     */
    public function getAllAdvertisementAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $advertisementPoints = $em->getRepository('AppBundle:Advertisement')->selectPoint();
            return $this->json(array('data' => $advertisementPoints), Response::HTTP_OK);
        }catch (\Exception $exception){
            return $this->json(array('error' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
