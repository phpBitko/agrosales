<?php
namespace AppBundle\Controller\Cabinet;

use AppBundle\Controller\Admin\BaseAdminController;
use AppBundle\Controller\ControllerTrait\GeneralFunction;
use AppBundle\Exception\ClientException;
use AppBundle\Service\Cabinet\ConfigFields\UserCabinet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\AdvertisementType;
use AppBundle\Entity\Advertisement;
use AppBundle\Service\PaginatorServices;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\Geometry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CabinetController
 * @Route("/cabinet")
 */
class CabinetController extends BaseAdminController
{
    use GeneralFunction;

    const STATUS_ADVERTISEMENT = [
        'ACTIVE' => 1,
        'PENDING' => 2,
        'REJECT' => 3,
        'DEACTIVATED' => 4
    ];

    /**
     * @Route("/", name="cabinet_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $this->getDataForSidebar();
        $data['user'] = $this->getUser();
        $data['totalViewCount'] = $em->getRepository('AppBundle:Advertisement')
                                    ->getTotalCountView($this->getUser()->getId());

        return $this->render('AppBundle:cabinet:dashboard.html.twig', ['data'=>$data]);
    }


    /**
     * @param Request $request
     * @param $id
     * @param string $entity
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \ReflectionException
     *
     * @Route ("/{entity}/{id}/edit", name="cabinet.object_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, $id, string $entity, UserPasswordEncoderInterface $passwordEncoder){
        return parent::editAction($request, $id,  $entity, $passwordEncoder);
    }


    /**
     * @param Request $request
     * @param Geometry $geometryServices
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/createAdvertisement", name="cabinet_create_advertisement", methods={"POST","GET"})
     */
    public function createAdvertisementAction(Request $request, Geometry $geometryServices)
    {
        $advertisement = new Advertisement();
        $em = $this->getDoctrine()->getManager();
        $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();
        $formAdvertisement = $this->createForm(AdvertisementType::class, $advertisement, array(
            'entity_manager' => $em,
        ));

        if ($request->isMethod('POST')) {
            $formAdvertisement->handleRequest($request);
            // Check form data is valid

            if ($formAdvertisement->isValid()) {

                $geomPolygonAdvertisement = $advertisement->getGeomPolygon();
                if(!empty($geomPolygonAdvertisement)){
                    $geomAdvertisement = $em->getRepository('AppBundle:Advertisement')->getCentroid($geomPolygonAdvertisement);
                    $advertisement->setGeom($geomAdvertisement);
                }else{
                    $geomAdvertisement = $advertisement->getGeom();
                }

                $region = $geometryServices->getPositionRegion($geomAdvertisement);

                if (null === $region) {
                    $this->addFlash('noticeMap', 'Необхідно вибрати місце розташування ділянки в межах України.');
                } else {
                    $advertisement->setDirRegion($region);
                    $advertisement->setUsers($this->getUser());

                    $district = $geometryServices->getPositionDistrict($geomAdvertisement);
                    $advertisement->setDirDistrict($district);

                    //Уcтановлюємо статус на розгляді
                    $advertisement->setDirStatus($em->getRepository('AppBundle:DirStatus')->find(2));

                    $em->persist($advertisement);

                    $em->flush();
                    $this->addFlash('success', 'Дані успішно збережені.');

                    return $this->redirectToRoute('cabinet_get_my_advertisement', array('selected' => 'pending'));
                }
            }

            $this->addFlash('danger', 'Перевірьте, будь ласка, правильність заповнення даних!');
        }

        $data = $this->getDataForSidebar();

        return $this->render('AppBundle:cabinet:create_new_advertisement.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'advertisement' => $advertisement,
            'purpose' => $purpose,
            'data' => $data
        ));
    }


    /**
     * @param Request $request
     * @param Advertisement $advertisement
     * @param Geometry $geometryServices
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/updateAdvertisement/{id}", requirements={"id": "[1-9]\d*"}, name="cabinet_update_advertisement_id", methods={"POST", "GET"})
     */
    public function updateAdvertisementAction(Request $request, Advertisement $advertisement, Geometry $geometryServices)
    {
        $em = $this->getDoctrine()->getManager();

        if ($advertisement->getUsers() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $formAdvertisement = $this->createForm(AdvertisementType::class, $advertisement, array(
            'entity_manager' => $em,
        ));

        $formAdvertisement->handleRequest($request);
        if ($request->isMethod('POST')) {
            // Check form data is valid
            if ($formAdvertisement->isValid()) {
                $geomPolygonAdvertisement = $advertisement->getGeomPolygon();
                if(!empty($geomPolygonAdvertisement)){
                    $geomAdvertisement = $em->getRepository('AppBundle:Advertisement')->getCentroid($geomPolygonAdvertisement);
                    $advertisement->setGeom($geomAdvertisement);
                }else{
                    $geomAdvertisement = $advertisement->getGeom();
                }

                $region = $geometryServices->getPositionRegion($geomAdvertisement);

                if (null === $region) {
                    $this->addFlash('noticeMap', 'Необхідно вибрати місце розташування ділянки в межах України.');
                } else {
                    $advertisement->setDirRegion($region);
                    $advertisement->setUsers($this->getUser());

                    $district = $geometryServices->getPositionDistrict($geomAdvertisement);
                    $advertisement->setDirDistrict($district);

                    //Учтановлюємо статус на розгляді
                    //$advertisement->setDirStatus($em->getRepository('AppBundle:DirStatus')->find(2));
                    $advertisement->setUpdateDate(new \DateTime());
                    // Save data to database
                    $em->persist($advertisement);
                    $em->flush();

                    // Inform user
                    $this->addFlash('success', 'Дані успішно збережені.');

                    return $this->redirectToRoute('cabinet_update_advertisement_id', array('id' => $advertisement->getId()));
                }

            }
            $this->addFlash('danger', 'Перевірьте, будь ласка, правильність заповнення даних!');
        }

        $data = $this->getDataForSidebar();

        return $this->render('AppBundle:cabinet:update_advertisement.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'data' => $data
        ));
    }


    /**
     * @param Request $request
     * @param string $selected
     * @return Response
     *
     * @Route("/getMyAdvertisement/{selected}", requirements={"selected": "active|pending|deactivated|reject"}, name="cabinet_get_my_advertisement", methods={"GET"})
     */
    public function getMyAdvertisementAction(Request $request, $selected, PaginatorServices $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $advertisementRepository = $em->getRepository('AppBundle:Advertisement');

        $userId = $this->getUser()->getId();

        $data = $this->getDataForSidebar();

        $data['status'] = $em->getRepository('AppBundle:DirStatus')
            ->find(self::STATUS_ADVERTISEMENT[strtoupper($selected)]);

        $query = $advertisementRepository->queryFindByStatus(self::STATUS_ADVERTISEMENT[strtoupper($selected)], ['addDate' => 'DESC'], $userId);
        $pagination = $paginator->getPagination($query, $request->query->getInt('page', 1));

        return $this->render('AppBundle:cabinet:view_advertisement.html.twig',
            ['advertisements' => $pagination,
                'data' => $data]);

    }


    /**
     * @param Request $request
     * @param Geometry $geometryServices
     * @return JsonResponse
     *
     * @Route("/getPosition", name="cabinet_get_position", methods={"POST"}, options={"expose"=true})
     *
     */
    public function getPositionAction(Request $request, Geometry $geometryServices)
    {
        try {
            $geom = $request->get('geom');
            $data = $geometryServices->getPositionAddress($geom);

            return $this->json(['address' => $data], Response::HTTP_OK);

        } catch (ClientException $exception) {
            return $this->json(['message' => $exception->getMessage(), 'status' => $exception->getStatusMessage()], $exception->getStatusCode());
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }


    /**
     * @param Advertisement $advertisement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/deactivateMyAdvertisement/{id}", requirements={"id": "[1-9]\d*"}, name="cabinet_deactivate_my_advertisement", methods={"GET"})
     *
     */
    public function deactivateMyAdvertisementAction(Advertisement $advertisement)
    {
        //Деактивувати можна тільки свої оголошення

        $this->checkUserWithAuthorException($advertisement);

        $this->setStatusAdvertisement($advertisement, self::STATUS_ADVERTISEMENT['DEACTIVATED']);

        $this->addFlash('success', 'Оголошення успішно деактивовано.');
        return $this->redirectToRoute('cabinet_get_my_advertisement', ['selected' => 'deactivated']);
    }

    /**
     * @param Advertisement $advertisement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/activateAdvertisement/{id}", requirements={"id": "[1-9]\d*"}, name="cabinet_activate_advertisement", methods={"GET"})
     *
     */
    public function activateAdvertisementAction(Advertisement $advertisement)
    {
        //Aктивувати можна тільки свої оголошення

        $this->checkUserWithAuthorException($advertisement);

        $this->setStatusAdvertisement($advertisement, self::STATUS_ADVERTISEMENT['PENDING']);

        $this->addFlash('success', 'Ваше оголошення буде розглянуто.');
        return $this->redirectToRoute('cabinet_get_my_advertisement', ['selected' => 'pending']);
    }


    public function getDataForSidebar(){
        $em = $this->getDoctrine()->getManager();

        $countAdvertisement = $em->getRepository('AppBundle:Advertisement')->getCountAdvertisementByStatus($this->getUser()->getId());

        $data['countAdvertisement'] = [];
        $countAll = 0;
        if (is_array($countAdvertisement)) {
            foreach ($countAdvertisement as $k => $v) {
                $data['countAdvertisement'][$v['id']] = $v;
                $countAll += $v['count'];
            }
        }
        $data['countAdvertisement']['countAll'] = $countAll;

        $countNotViewMessages = $em->getRepository('AppBundle:Messages')->getCountNotViewMessages($this->getUser()->getId());
        $data['countNotViewMessages'] = [];
        if (is_array($countNotViewMessages)) {
            foreach ($countNotViewMessages as $k => $v) {
                $data['countNotViewMessages'][$v['status']] = $v;
            }
        }

        return $data;
    }
}