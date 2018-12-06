<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Advertisement;
use AppBundle\Filter\AdvertisementFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdvertisementController
 * @Route("/advertisement")
 */

class AdvertisementController extends Controller
{
    /**
     * @Route("/",  defaults={"page": 1}, name="advertisement_index", methods={"GET"})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="advertisement_index_paginated")
     *
     */
    public function indexAction(Request $request, $page = 1)
    {
        /* $file = __DIR__.'\..\Resources\public\18.json';
         dump(__DIR__.'\..\Resources\public\test.json');
         $stream = fopen($file, 'r');
         dump($stream);
         $listener = new \JsonStreamingParser\Listener\InMemoryListener();;
         try {
             $parser = new \JsonStreamingParser\Parser($stream, $listener);
             $parser->parse();
             fclose($stream);
         } catch (Exception $e) {
             fclose($stream);
             throw $e;
         }

         dump($listener->getJson());*/

        $em = $this->getDoctrine()->getManager();

        $auxiliaryFunctionService = $this->get('app.service.auxiliary_function');
        $advertisement3 = $em->getRepository('AppBundle:Advertisement')->findLatest($page);
        // replace this example code with whatever you need
        dump($advertisement3);
        return $this->render('AppBundle:advertisement:index.html.twig', array('advertisement' => $advertisement3));
    }
    /**
     * @Route("/list",  defaults={"page": 1}, name="advertisement_list", methods={"GET"})
     * @Route("/list/page/{page}", requirements={"page": "[1-9]\d*"}, name="advertisement_index_paginated_list")
     *
     */
    public function advertisementListAction(Request $request, $page = 1)
    {

        $em = $this->getDoctrine()->getManager();
        $auxiliaryFunctionService = $this->get('app.service.auxiliary_function');
        $advertisement = $em->getRepository('AppBundle:Advertisement')->findLatest($page);
        // replace this example code with whatever you need
        dump($advertisement);
        return $this->render('AppBundle:advertisement:list.html.twig', array('advertisement' => $advertisement));
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
        } catch (\Exception $exception) {
            return $this->json(array('error' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/details/{id}", requirements={"id": "[1-9]\d*"}, name="advertisement_details", methods={"GET"})
     *
     */
    public function advertisementDetailsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $advertisementDetails = $em->getRepository('AppBundle:Advertisement')->findOneBy(array('id' => $id));
        dump($advertisementDetails);

        if ($advertisementDetails == null) {
            throw new NotFoundHttpException();
        }
        return $this->render('AppBundle:advertisement:details.html.twig', array('advertisement' => $advertisementDetails));
//            if ($advertisementDetails->isActive()== true) {
//                return $this->render('AppBundle:advertisement:details.html.twig', array('advertisement' => $advertisementDetails));
//            } else {
//                return $this->redirectToRoute('get_all_advertisement');
//            }
    }

    /**
     * @Route("/filter", name="advertisement_filter", methods={"GET"})
     *
     */
    public function testFilterAction(Request $request)
    {

        $form = $this->get('form.factory')->create(AdvertisementFilterType::class);

        if ($request->query->has($form->getName())) {           // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('ProjectSuperBundle:MyEntity')
                ->createQueryBuilder('e');

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);

            // now look at the DQL =)
            var_dump($filterBuilder->getDql());
        }

        return $this->render('AppBundle:advertisement:filter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/getDetailsAdvertisement", name="map_details_advertisement", options={"expose"=true}, methods={"POST"})
     *
     */
    public function getDetailsAdvertisementAction(Request $request)
    {
        /*        try {*/
        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $advertisementDetails = $em->getRepository('AppBundle:Advertisement')->find($id);
        dump($advertisementDetails);
        $table['details'] = $this->renderView('AppBundle:map:details.html.twig', array('advertisementDetails' => $advertisementDetails));

        //return $this->renderView('AppBundle:map:details.html.twig', array('advertisementDetails' => $advertisementDetails));
        return new JsonResponse(['table' => $table], Response::HTTP_OK);

        //return $this->json(array('data' => $advertisementDetails), Response::HTTP_OK);
        /*        } catch (\Exception $exception) {
                    return $this->json(array('error' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
                }*/

    }




}
