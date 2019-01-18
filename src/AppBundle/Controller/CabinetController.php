<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 15.08.2018
 * Time: 17:19
 */

namespace AppBundle\Controller;

use AppBundle\Exception\WarningException;
use AppBundle\Service\HandleForm;
use Symfony\Component\EventDispatcher\Tests\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\AdvertisementType;
use AppBundle\Entity\Advertisement;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\Geometry;


/**
 * Class CabinetController
 * @Route("/cabinet")
 */
class CabinetController extends SuperController
{

    /**
     * @Route("/", name="cabinet_index", methods={"GET"})
     */
    public function indexAction()
    {

        return $this->redirectToRoute('cabinet_get_my_advertisement');
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

                $geomAdvertisement = $advertisement->getGeom();
                $region = $geometryServices->getPositionRegion($geomAdvertisement);

                if  (null === $region) {
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

                    return $this->redirectToRoute('cabinet_get_my_advertisement', array('selected' => 'pending-tab'));
                }
            }

            $this->addFlash('danger', 'Перевірьте, будь ласка, правильність заповнення даних!');
        }

        return $this->render('AppBundle:cabinet:create_new_advertisement.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'advertisement' => $advertisement,
            'purpose' => $purpose
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

        if ($request->isMethod('POST')) {
            $formAdvertisement->handleRequest($request);

            // Check form data is valid
            if ($formAdvertisement->isValid()) {
                $geomAdvertisement = $advertisement->getGeom();
                $region = $geometryServices->getPositionRegion($geomAdvertisement);

                if  (null === $region) {
                    $this->addFlash('noticeMap', 'Необхідно вибрати місце розташування ділянки в межах України.');
                } else {
                    $advertisement->setDirRegion($region);
                    $advertisement->setUsers($this->getUser());

                    $district = $geometryServices->getPositionDistrict($geomAdvertisement);
                    $advertisement->setDirDistrict($district);

                    //Учтановлюємо статус на розгляді
                    $advertisement->setDirStatus($em->getRepository('AppBundle:DirStatus')->find(2));
                    $advertisement->setUpdateDate(new \DateTime());
                    // Save data to database
                    $em->persist($advertisement);
                    $em->flush();

                    // Inform user
                    $this->addFlash('success', 'Дані успішно збережені. Ваше оголошення буде розглянуто.');

                    return $this->redirectToRoute('cabinet_update_advertisement_id', array('id' => $advertisement->getId()));
                }

            }
            $this->addFlash('danger', 'Перевірьте, будь ласка, правильність заповнення даних!');
        }

        return $this->render('AppBundle:cabinet:update_advertisement.html.twig', array(
            'form' => $formAdvertisement->createView(),
        ));
    }


    /**
     * @param Request $request
     * @param string $selected
     * @return Response
     *
     * @Route("/getMyAdvertisement/{selected}", requirements={"selected": "active-tab|pending-tab|deactivated-tab"}, name="cabinet_get_my_advertisement", methods={"GET"})
     */
    public function getMyAdvertisementAction(Request $request, $selected = 'active-tab')
    {

        $em = $this->getDoctrine()->getManager();
        //Активні
        $myAdvertisement['myAdvertisementActive'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser' => $this->getUser()->getId(), 'dirStatus' => 1), array('addDate' => 'DESC'));
        //Очікують, повернуті
        $myAdvertisement ['myAdvertisementPending'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser' => $this->getUser()->getId(), 'dirStatus' => [2, 3]), array('addDate' => 'DESC'));
        //Деактивовані
        $myAdvertisement ['myAdvertisementDeactivated'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser' => $this->getUser()->getId(), 'dirStatus' => 4), array('addDate' => 'DESC'));

        return $this->render('AppBundle:cabinet:view_my_advertisement.html.twig', array('advertisements' => $myAdvertisement, 'selected' => $selected));
    }


    /**
     * @param Request $request
     * @param Geometry $geometryServices
     * @return \Symfony\Component\HttpFoundation\JsonResponse
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

        } catch (WarningException $exception) {
            return $this->json(['message' => $exception->getMessage(), 'status' => self::RESPONSE_STATUS_WARNING], $exception->getStatusCode());
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
    public function deactivateMyAdvertisementAction(Advertisement $advertisement){
        //Деактивувати можна тільки свої оголошення
        if ($advertisement->getUsers() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        $this->setStatusAdvertisement($advertisement, self::STATUS_ADVERTISEMENT['DEACTIVATED']);

        $this->addFlash('success', 'Оголошення деактивовано. Щоб активувати чи переглянути його, перейдіть в закладку "Деактивовані"!');
        return $this->redirectToRoute('cabinet_get_my_advertisement');
    }

    /**
     * @param Advertisement $advertisement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/activateAdvertisement/{id}", requirements={"id": "[1-9]\d*"}, name="cabinet_activate_advertisement", methods={"GET"})
     *
     */
    public function activateAdvertisementAction(Advertisement $advertisement){
        //Aктивувати можна тільки свої оголошення

        if ($advertisement->getUsers() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $this->setStatusAdvertisement($advertisement, self::STATUS_ADVERTISEMENT['PENDING']);

        $this->addFlash('success', 'Ваше оголошення буде розглянуто.');
        return $this->redirectToRoute('cabinet_get_my_advertisement');
    }

}