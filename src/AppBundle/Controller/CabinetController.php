<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 15.08.2018
 * Time: 17:19
 */

namespace AppBundle\Controller;


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
        $formAdvertisement = $this->createForm(AdvertisementType::class, $advertisement);
        //заповнюєв надини форму
        $formAdvertisement->handleRequest($request);
        if ($formAdvertisement->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advertisement);
            $em->flush();
            return $this->render('AppBundle:cabinet:index.html.twig', array('form' => $formAdvertisement->createView()));
        }
        dump($formAdvertisement);

        return $this->render('AppBundle:cabinet:index.html.twig', array(
            'form' => $formAdvertisement->createView(),
            'advertisement' => $advertisement
        ));
    }

}