<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 15.08.2018
 * Time: 17:19
 */

namespace AppBundle\Controller;


use AppBundle\AppBundle;
use AppBundle\Entity\DirPurpose;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\AdvertisementType;
use AppBundle\Entity\Advertisement;
use AppBundle\Entity\Photos;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;


/**
 * Class CabinetController
 * @Route("/cabinet")
 */
class CabinetController extends Controller
{

    /**
     * @Route("/", name="cabinet_index", methods={"POST","GET"})
     */
    public function indexAction(Request $request)
    {

        return $this->redirectToRoute('cabinet_get_my_advertisement');
    }


    /**
     * @Route("/createAdvertisement", name="cabinet_create_advertisement", methods={"POST","GET"})
     */
    public function createAdvertisementAction(Request $request){

        $advertisement = new Advertisement();
        $em = $this->getDoctrine()->getManager();
        $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();
        $formAdvertisement = $this->createForm(AdvertisementType::class, $advertisement, array(
            'entity_manager' => $em,
        ));

        //Обробляємо форму

        if ($request->isMethod('POST')){
            $formAdvertisement->handleRequest($request);

            if ($formAdvertisement->isValid()) {
                $advertisement->setUsers($this->getUser());
                $advertisement->setDirDistrict($em->getRepository('AppBundle:DirDistrict')->find(2));

                //Учтановлюємо статус на розгляді
                $advertisement->setDirStatus($em->getRepository('AppBundle:DirStatus')->find(2));

                $em->persist($advertisement);

                $em->flush();
                $this->addFlash('success', 'Дані успішно збережені.');

                return $this->redirectToRoute('cabinet_get_my_advertisement', array('selected' => 'pending-tab'));
            }
        }
        return $this->render('AppBundle:cabinet:create_new_advertisement.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'advertisement' => $advertisement,
            'purpose' => $purpose
        ));
    }

    /**
     * @Route("/updateAdvertisement/{id}", requirements={"id": "[1-9]\d*"}, name="cabinet_update_advertisement_id", methods={"POST","GET"})
     */
    public function updateAdvertisementAction(Request $request, $id){

        $em = $this->getDoctrine()->getManager();
        $advertisement = $em->getRepository('AppBundle:Advertisement')->find($id);

        if($advertisement->getUsers() !==  $this->getUser() ){
            throw $this->createAccessDeniedException();
        }

        $formAdvertisement = $this->createForm(AdvertisementType::class, $advertisement, array(
            'entity_manager' => $em,
        ));

        if ($request->isMethod('POST'))
        {
            $formAdvertisement->handleRequest($request);
            // Check form data is valid
            if ($formAdvertisement->isValid()){
                $advertisement->setUsers($this->getUser());
                $advertisement->setDirDistrict($em->getRepository('AppBundle:DirDistrict')->find(2));

                //Учтановлюємо статус на розгляді
                $advertisement->setDirStatus($em->getRepository('AppBundle:DirStatus')->find(2));
                // Save data to database
                $em->persist($advertisement);
                $em->flush();

                // Inform user
                $this->addFlash('success', 'Дані успішно збережені.');

                // Redirect to view page
                return $this->redirectToRoute('cabinet_update_advertisement_id', array('id'=>$id));
            }
        }

        return $this->render('AppBundle:cabinet:update_advertisement.html.twig', array(
            'form' => $formAdvertisement->createView(),
        ));
    }


    /**
     * @param string $selected
     * @return Response
     *
     * @Route("/getMyAdvertisement/{selected}", requirements={"selected": "active-tab|pending-tab|"}, name="cabinet_get_my_advertisement", methods={"GET"})
     *
     */
    public function getMyAdvertisementAction(Request $request, $selected = 'active-tab'){
        $em = $this->getDoctrine()->getManager();
        $myAdvertisement['myAdvertisementActive'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser'=>$this->getUser()->getId(), 'dirStatus'=>1), array('addDate'=>'DESC'));
        $myAdvertisement ['myAdvertisementPending'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser'=>$this->getUser()->getId(), 'dirStatus'=>[2, 3]), array('addDate'=>'DESC'));
        $myAdvertisement ['myAdvertisementDeactivated'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser'=>$this->getUser()->getId(), 'dirStatus'=> 4), array('addDate'=>'DESC'));

        return $this->render('AppBundle:cabinet:view_my_advertisement.html.twig', array('advertisements'=>$myAdvertisement, 'selected' => $selected));
    }

    /**
     * @Route("/getPosition", name="cabinet_get_position", methods={"POST"}, options={"expose"=true})
     *
     */
    public function  getPositionAction(Request $request){
        try {
            $geom = $request->get('geom');
            $em = $this->getDoctrine()->getManager();
            $region = $em->getRepository('AppBundle:DirRegion')->getPositionByGeom($geom);
            if($region === false){
                throw new \Exception('Ділянка повинна знаходитись в межах України!');
            }
            $data['region'] = $region->getNatoobl();
            $district = $em->getRepository('AppBundle:DirDistrict')->getPositionByGeom($geom);
            if(!empty($district)){
                $data['district'] = $district->getNatoray();
            }

            return $this->json(['address' => $data], Response::HTTP_OK);
        }catch (\Exception $exception){

            return $this->json(['error'=>$exception->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

}