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
     * @Route("/{id}", defaults={"id": null}, requirements={"id": "[1-9]\d*"}, name="map_index", methods={"GET"})
     * @return Response
     */
    public function indexAction(Request $request, Advertisement $advertisement = null)

    {
        //$adv = new Advertisement();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AdvertisementFilterType::class, null, ['entity_manager' => $em]);

        if ($request->query->has($form->getName())) {           // manually bind values from the request

            $form->submit(($form->getName()));


            // initialize a query builder

            $filterBuilder = $advertisement->qbFindByStatus(self::STATUS_ADVERTISEMENT['ACTIVE']);

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);


        } else {

        }

        $filterAttributes = $this->parseQueryString($request);

        $data = compact('typeView', 'filterAttributes');


        return $this->render('AppBundle:map:index.html.twig', array(
            'advertisement' => $advertisement,
            'form' => $form->createView(),
            'data' => $data,
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


    /**
     *
     * @param $formData
     * @Route("/getFilterAdvertisement", name="map_filter_advertisement", options={"expose"=true}, methods={"POST"})
     *
     * @return JsonResponse
     */
    public function getFilterAdvertisementAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $advertisement = $em->getRepository('AppBundle:Advertisement');
        $form = $this->createForm(AdvertisementFilterType::class, null, ['entity_manager' => $em]);

        try {
            $filterBuilder = $advertisement->qbFindAllByNotNull('geom',0,self::STATUS_ADVERTISEMENT['ACTIVE']);
            if ($request->request->has($form->getName())) {
                $form->submit($request->request->get($form->getName()));
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
            }
            $query = $filterBuilder->getQuery();
            $advertisement = $query->getResult();

            if (empty($advertisement)) {
                throw new WarningException('Нічого не знайдено!');
            }

            return $this->json(array('data' => $advertisement), Response::HTTP_OK);

        }catch (WarningException $exception) {
                return $this->json(['message' => $exception->getMessage(), 'status' => self::RESPONSE_STATUS_WARNING], $exception->getStatusCode());
        } catch (\Exception $exception) {
            return $this->json(array('message' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Парсить строку url з параметрами, та готує дані для відображення параметрів фільтрації
     *
     * @param Request $request
     * @return array
     */
    protected function parseQueryString(Request $request)
    {
        $filterParams = $request->query->get('item_filter');

        $resultFilter = [];
        $arrParam = ['addDate', 'area', 'price', 'dirPurpose', 'isRoad', 'isElectricity', 'isGas', 'isSewerage', 'isWaterSupply'];
        try {
            if ($filterParams !== null) {
                $str = $request->getQueryString();
                $strMassOrig = explode('%', $str);

                foreach ($strMassOrig as $k => $v) {
                    $strMassCut[$k] = ltrim($v, '5B');
                }

                foreach ($strMassCut as $k => $v) {
                    if (in_array($v, $arrParam) and (!isset($resultFilter[$v]['strHref']))) {
                        $strMassOrigReplice = $strMassOrig;
                        if ($v == 'price') {
                            $strMassOrigReplice[$k + 3] = '5D=&item_filter';
                            $strMassOrigReplice[$k + 7] = '5D=&submit-filter=';
                        } elseif ($v == 'dirPurpose') {
                            $keyPurpose = $k;
                            $rowNumber = 0;
                            while ($strMassOrigReplice[$keyPurpose] == '5BdirPurpose') {
                                $keyPurpose += 4;
                                $rowNumber++;
                            }
                            array_splice($strMassOrigReplice, $k, $rowNumber * 4);
                        } elseif ($v == 'isRoad' or $v == 'isWaterSupply' or $v == 'isElectricity' or $v == 'isGas' or $v == 'isSewerage') {
                            unset($strMassOrigReplice[$k]);
                            unset($strMassOrigReplice[$k + 1]);

                        } else {
                            $strMassOrigReplice[$k + 3] = '5D=&item_filter';
                            $strMassOrigReplice[$k + 7] = '5D=&item_filter';
                        }

                        if ($v == 'addDate') {
                            if ($filterParams[$v]['left_datetime'] != '') {
                                $resultFilter[$v]['left'] = $filterParams[$v]['left_datetime'];
                            }
                            if ($filterParams[$v]['right_datetime'] != '') {
                                $resultFilter[$v]['right'] = $filterParams[$v]['right_datetime'];
                            }
                        } elseif ($v == 'dirPurpose') {
                            $resultFilter[$v]['purpose'] = $filterParams[$v];
                        } elseif ($v == 'isRoad' or $v == 'isWaterSupply' or $v == 'isElectricity' or $v == 'isGas' or $v == 'isSewerage') {
                            $resultFilter[$v]['param'] = $filterParams[$v];
                        } else {
                            if ($filterParams[$v]['left_number'] != '') {
                                $resultFilter[$v]['left'] = $filterParams[$v]['left_number'];
                            }
                            if ($filterParams[$v]['right_number'] != '') {
                                $resultFilter[$v]['right'] = $filterParams[$v]['right_number'];
                            }
                        }
                        if (array_key_exists($v, $resultFilter)) {
                            $resultFilter[$v]['strHref'] = implode('%', $strMassOrigReplice);
                        }
                    }
                }
                $resultFilter = $this->parseResultFilter($resultFilter);
            }
            return $resultFilter;
        } catch (\Exception $exception) {

            return $resultFilter = [];
        }
    }


}