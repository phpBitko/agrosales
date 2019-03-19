<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Advertisement;
use AppBundle\Exception\WarningException;
use AppBundle\Filter\AdvertisementFilterType;
use AppBundle\Service\ParseFilterServices;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\Helpers\FormErrorHelper;

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
    public function indexAction(Request $request, SessionInterface $session, Advertisement $advertisement = null)
    {
        $form = $this->createForm(AdvertisementFilterType::class);

        if ($request->get($form->getName())) {
            $session->set($form->getName(), $request->get($form->getName()));
            $form->submit($request->get($form->getName()));
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
            $id = $request->request->getInt('id');

            if (!($id > 0)) {
                return $this->json(array('message' => 'Передано не вірний індетифікатор!'), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $advertisementDetails = $this->em->getRepository('AppBundle:Advertisement')->find($id);

            if ($advertisementDetails === null) {
                return $this->json(array('message' => 'Оголошення не знайдено!'), Response::HTTP_NOT_FOUND);
            }

            /**Отримуємо інформацію з АПІ */
            $data = [];
            if ($request->getHost() == '192.168.33.37' || $request->getHost() == '212.26.131.134' ) {
                $osmRequestClient = $this->get('app.service.api_client.osm_request_client');
                if (!$osmRequestClient->isConnect()) {
                    return $this->json([
                        'message' => 'Виникла помилка доступу до API E-сервісу!'
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $coord = preg_split("/[(\s,)]/", $advertisementDetails->getGeom());
                $coord = [$coord[1], $coord[2]];
                $data['closestObject'] = $osmRequestClient->getClosestObject(['coord' => $coord]);
                $data['coord'] = $coord;

                if (false === $data['closestObject']) {
                    return $this->json([
                        'message' => $osmRequestClient->getLastErrorMessage()
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $table['details'] = $this->renderView('AppBundle:map:_details.html.twig', ['advertisementDetails' => $advertisementDetails]);
            return new JsonResponse(['table' => $table, 'data' =>$data], Response::HTTP_OK);

        } catch (WarningException $exception) {
            return $this->json(['message' => $exception->getMessage(), 'status' => self::RESPONSE_STATUS_WARNING], $exception->getStatusCode());
        } catch (\Exception $exception) {
            return $this->json(array('message' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @Route("/getClosestObject", name="map.get_closest_object", options={"expose"=true}, methods={"POST"})
     *
     * @return JsonResponse
     */
    public function getClosestObject(Request $request)
    {
        try {
            $coord = $request->get('coord');
            $osmRequestClient = $this->get('app.service.api_client.osm_request_client');
            if (!$osmRequestClient->isConnect()) {
                return $this->json([
                    'message' => 'Виникла помилка доступу до API E-сервісу!'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $data['closestObject'] = $osmRequestClient->getClosestObject(['coord' => $coord]);

            if (false === $data['closestObject']) {
                return $this->json([
                    'message' => $osmRequestClient->getLastErrorMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse($data);
        } catch (Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @param $formData
     * @Route("/getFilterAdvertisement", name="map_filter_advertisement", options={"expose"=true}, methods={"POST"})
     *
     * @return JsonResponse
     */
    public function getFilterAdvertisementAction(Request $request, SessionInterface $session, ParseFilterServices $parseFilterServices)
    {
        $advertisement = $this->em->getRepository('AppBundle:Advertisement');
        $form = $this->createForm(AdvertisementFilterType::class);

        $filterArray = $session->get($form->getName()) ? $session->get($form->getName()) : $request->get($form->getName());

        try {
            $filterBuilder = $advertisement->qbFindAllByNotNull('geom', 0, self::STATUS_ADVERTISEMENT['ACTIVE']);

            $filterArray = $parseFilterServices->normalizeFilterParam($filterArray);

            $form->submit($filterArray);

            if ($filterArray) {
                if ($form->isValid()) {
                    $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
                } else {
                    $this->errors = array_values(FormErrorHelper::getErrorMessages($form, true));
                }
                $session->remove($form->getName());
            }
            $query = $filterBuilder->getQuery();
            $advertisement = $query->getResult();

            if (empty($advertisement)) {
                throw new WarningException('Нічого не знайдено!');
            }

            return $this->json(['data' => $advertisement, 'errors' => $this->errors], Response::HTTP_OK);

        } catch (WarningException $exception) {
            return $this->json(['message' => $exception->getMessage(), 'status' => self::RESPONSE_STATUS_WARNING], $exception->getStatusCode());
        } catch (\Exception $exception) {
            return $this->json(array('message' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}