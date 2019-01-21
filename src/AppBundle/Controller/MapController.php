<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 06.09.2018
 * Time: 14:21
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Advertisement;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Class MapController
 * @Route("/map")
 *
 */
class MapController extends Controller
{
    /**
     * @param Request $request
     * @param Advertisement $advertisement
     * @Route("/{id}", defaults={"id": null}, requirements={"id": "[1-9]\d*"}, name="map_index", methods={"GET"})
     * @return Response
     */
    public function indexAction(Request $request, Advertisement $advertisement = null)

    {

        return $this->render('AppBundle:map:index.html.twig', array('advertisement' => $advertisement));
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
                return $this->json(array('error' => 'Оголошення не знайдено!'), Response::HTTP_NOT_FOUND);
            }
            $table['details'] = $this->renderView('AppBundle:map:details.html.twig', array('advertisementDetails' => $advertisementDetails));

            return new JsonResponse(['table' => $table], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->json(array('error' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @Route("/getAdvertisementByStatus", name="get_advertisement_by_status", options={"expose"=true}, methods={"POST"})
     *
     * @return JsonResponse
     */
    public function getAdvertisementByStatusAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $advertisement = $em->getRepository('AppBundle:Advertisement')->findAllByNotNull('geom', 0);
            if ($advertisement === null) {
                throw new NotFoundHttpException();
            }

            return $this->json(array('data' => $advertisement), Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->json(array('error' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}