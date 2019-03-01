<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 06.09.2018
 * Time: 14:21
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Advertisement;
use AppBundle\Exception\ViewException;
use AppBundle\Exception\WarningException;
use AppBundle\Filter\AdvertisementFilterType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class MapController
 * @Route("/map")
 *
 */
class MapController extends SuperController
{
    /**
     * @param Request $request
     * @param Advertisement $advertisement
     * @Route("/{id}", defaults={"id": null}, requirements={"id" : "[1-9]\d*"}, name="map_index", methods={"POST", "GET"})
     * @return Response
     */
    public function indexAction(Request $request, Advertisement $advertisement = null)
    {

        $em = $this->em;
        $form = $this->createForm(AdvertisementFilterType::class, null, ['entity_manager' => $em]);

        if ($request->request->get($form->getName())) {
            $session = new Session();
            $session->set($form->getName(), $request->request->get($form->getName()));
            $form->submit($request->request->get($form->getName()));
        }

        return $this->render('AppBundle:map:index.html.twig', array(
            'advertisement' => $advertisement,
            'form' => $form->createView(),

        ));
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
            $table['details'] = $this->renderView('AppBundle:map:_details.html.twig', array('advertisementDetails' => $advertisementDetails));

            return new JsonResponse(['table' => $table], Response::HTTP_OK);

        } catch (WarningException $exception) {
            return $this->json(['message' => $exception->getMessage(), 'status' => self::RESPONSE_STATUS_WARNING], $exception->getStatusCode());
        } catch (\Exception $exception) {
            return $this->json(array('message' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @Route("/getAdvertisementByStatus", name="get_advertisement_by_status", options={"expose"=true}, methods={"POST"})
     *
     * @return JsonResponse
     */
    /*    public function getAdvertisementByStatusAction()
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
        }*/


    /**
     *
     * @param $formData
     * @Route("/getFilterAdvertisement", name="map_filter_advertisement", options={"expose"=true}, methods={"POST"})
     *
     * @return JsonResponse
     */
    public function getFilterAdvertisementAction(Request $request)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $advertisement = $em->getRepository('AppBundle:Advertisement');
        $form = $this->createForm(AdvertisementFilterType::class, null, ['entity_manager' => $em]);
        $filterArray = [];
        $errors = [];
        $filterArray = $session->get($form->getName()) ? $session->get($form->getName()) : $request->request->get($form->getName());
        try {
            $filterBuilder = $advertisement->qbFindAllByNotNull('geom', 0, self::STATUS_ADVERTISEMENT['ACTIVE']);

            if ($filterArray) {
                $form->submit($filterArray);
                if ($form->isValid()) {
                    $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
                } else {
                    $errors = $this->parseErrors($form->getErrors(true));
                }
                $session->remove($form->getName());
            } else {
                $form->submit($filterArray);
            }
            $query = $filterBuilder->getQuery();
            $advertisement = $query->getResult();

            if (empty($advertisement)) {
                throw new WarningException('Нічого не знайдено!');
            }

            return $this->json(['data' => $advertisement, 'errors' => $errors], Response::HTTP_OK);

        } catch (WarningException $exception) {
            return $this->json(['message' => $exception->getMessage(), 'status' => self::RESPONSE_STATUS_WARNING], $exception->getStatusCode());
        } catch (\Exception $exception) {
            return $this->json(array('message' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Повертає масив з переліком помилок
     *
     * @param $arrayErrors
     * @return array
     */
    private function parseErrors($arrayErrors)
    {
        $errors = [];
        if ($arrayErrors) {
            foreach ($arrayErrors as $key => $error) {
                $template = $error->getMessage();
                $errors[$key] = $template;
            }
        }
        return $errors;
    }


}