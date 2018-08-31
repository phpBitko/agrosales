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

        //заповнюєв надини форму
        $formAdvertisement->handleRequest($request);
        if ($formAdvertisement->isValid()) {
            $advertisement->setUsers($this->getUser());
            $advertisement->setDirDistrict($em->getRepository('AppBundle:DirDistrict')->find(2));
            dump($formAdvertisement);
            dump($advertisement);
            $em->persist($advertisement);
            $em->flush();
            return $this->render('AppBundle:cabinet:index.html.twig', array(
                'form' => $formAdvertisement->createView(),
                'purpose' => $purpose
            ));
        }

        dump($advertisement);

        return $this->render('AppBundle:cabinet:index.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'advertisement' => $advertisement,
            'purpose' => $purpose

        ));
    }

}