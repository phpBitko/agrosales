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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;

use Vich\UploaderBundle\Templating\Helper as helper;


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
        $advertisement = new Advertisement();
        $em = $this->getDoctrine()->getManager();
        $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();
        $formAdvertisement = $this->createForm(AdvertisementType::class, $advertisement, array(
            'entity_manager' => $em,
        ));

        //заповнюєм форму
        $formAdvertisement->handleRequest($request);

        if ($formAdvertisement->isValid() && $formAdvertisement->isSubmitted()) {
            $advertisement->setUsers($this->getUser());
            $advertisement->setDirDistrict($em->getRepository('AppBundle:DirDistrict')->find(2));
            $files = $advertisement->getPhotos();

            $advertisement->setPhotos(new ArrayCollection());

            //$advertisement->setGeom('point('.$advertisement->getCoordB() . ' ' . $advertisement->getCoordL().')');
            $em->persist($advertisement);
            foreach ($files as $file) {

//                if ($file->getClientMimeType() != 'image/jpeg') {
//                    throw new InValidFormException('photos', 'Фото повинно бути у форматі jpeg', 400);
//                }
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

            return $this->render('AppBundle:cabinet:index.html.twig', array(
                'form' => $formAdvertisement->createView(),
                'purpose' => $purpose
            ));
        }
        return $this->render('AppBundle:cabinet:index.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'advertisement' => $advertisement,
            'purpose' => $purpose

        ));
    }


    /**
     * @Route("/createAdvertisement", name="cabinet_create_advertisement", methods={"POST","GET"})
     */
    public function createAdvertisement(Request $request){
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

            return $this->render('AppBundle:cabinet:create_new_advertisement.html.twig', array(
                'form' => $formAdvertisement->createView(),
                'purpose' => $purpose
            ));
        }
        return $this->render('AppBundle:cabinet:create_new_advertisement.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'advertisement' => $advertisement,
            'purpose' => $purpose

        ));

    }

}