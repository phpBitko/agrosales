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
use AppBundle\Form\AddAdvertisementType;



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
        $addAdvertisement= new AddAdvertisementType();
        $form=$this->createForm('AppBundle\Form\AddAdvertisementType',$addAdvertisement);

        return $this->render('AppBundle:cabinet:index.html.twig',array('form'=>$form->createView()));
    }

}