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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
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
            $photosAll = array();
            $advertisement->setPhotos(new ArrayCollection());
            foreach ($files as $file) {

//                if ($file->getClientMimeType() != 'image/jpeg') {
//                    throw new InValidFormException('photos', 'Фото повинно бути у форматі jpeg', 400);
//                }
                $photoOne = new Photos();
                $fileName = $photoOne->getPhotoName() . uniqid() . '.' . $file->guessExtension();
                //$fileName = md5(uniqid()).'.'.$file->guessExtension();
                $photoOne->setPhotoName($fileName);

                $advertisement->addPhoto($photoOne);
                dump($advertisement);
            }

            $em->persist($advertisement);
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

}