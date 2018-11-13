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

        //заповнюєм форму
        $formAdvertisement->handleRequest($request);
        if ($formAdvertisement->isValid()) {
            $advertisement->setUsers($this->getUser());
            $advertisement->setDirDistrict($em->getRepository('AppBundle:DirDistrict')->find(2));
            $files = $advertisement->getPhotos();
            $advertisement->setPhotos(new ArrayCollection());

            //$advertisement->setGeom('point('.$advertisement->getCoordB() . ' ' . $advertisement->getCoordL().')');
            $em->persist($advertisement);
          //  dump($files);
            foreach ($files as $file) {
                $photoOne = new Photos();
                $fileName = uniqid() . '.' . $file->guessExtension();
                $photoOne->setPhotoNameOriginal($file->getClientOriginalName());
                $photoOne->setPhotoNameNew($fileName);
                $advertisement->addPhoto($photoOne);
                $date = $photoOne->getAddDate();
                $dateFolder = $date->format('Y-m-d');
                $file->move($this->getParameter('photos_directory') . $dateFolder .'/'.$advertisement->getId() , $photoOne->getPhotoNameNew());
            }
            $em->flush();
            $this->addFlash('success', 'Дані успішно збережені.');

            return $this->redirectToRoute('cabinet_get_my_advertisement', array('selected' => 'pending-tab'));
        }
        return $this->render('AppBundle:cabinet:create_new_advertisement.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'advertisement' => $advertisement,
            'purpose' => $purpose
        ));
    }


    /**
     * @param string $selected
     * @return Response
     *
     * @Route("/getMyAdvertisement/{selected}", name="cabinet_get_my_advertisement", methods={"GET"})
     *
     */
    public function getMyAdvertisementAction(Request $request, $selected = 'active-tab'){
        $em = $this->getDoctrine()->getManager();
        $myAdvertisement['myAdvertisementActive'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser'=>$this->getUser()->getId(), 'isActive'=>true), array('addDate'=>'DESC'));
        $myAdvertisement ['myAdvertisementPending'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser'=>$this->getUser()->getId(), 'isPending'=>true), array('addDate'=>'DESC'));
        $myAdvertisement ['myAdvertisementDeactivated'] = $em->getRepository('AppBundle:Advertisement')
            ->findBy(array('idUser'=>$this->getUser()->getId(), 'isActive'=>false, 'isPending'=>false), array('addDate'=>'DESC'));

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