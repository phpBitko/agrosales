<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 06.09.2018
 * Time: 14:21
 */

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


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

    /**
     * @Route("/getDetailsAdvertisement", name="map_details_advertisement", options={"expose"=true}, methods={"POST"})
     *
     */
    public function getDetailsAdvertisementAction(Request $request)
    {
        try {
            $id = $request->get('id');

            $em = $this->getDoctrine()->getManager();

            //треба перевірити $id бо може прийти що попало. мабуть на > 0

            $advertisementDetails = $em->getRepository('AppBundle:Advertisement')->find($id);
            if ($advertisementDetails === null) {
                return $this->json(array('error'=>'Оголошення не знайдено!'),Response::HTTP_NOT_FOUND);
            }
            $table['details'] = $this->renderView('AppBundle:map:details.html.twig', array('advertisementDetails' => $advertisementDetails));

            return new JsonResponse(['table' => $table], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->json(array('error' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

}