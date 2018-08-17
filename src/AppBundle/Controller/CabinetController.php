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
     * @Route("/", name="cabinet_index", methods={"GET"})
     */
    public function indexAction(Request $request)

    {
        $advertisement= new Advertisement();
        $formAdvertisement =$this->createForm(AdvertisementType::class,$advertisement);
        dump($formAdvertisement );

        return $this->render('AppBundle:cabinet:index.html.twig',array('form'=>$formAdvertisement->createView()));
    }

}