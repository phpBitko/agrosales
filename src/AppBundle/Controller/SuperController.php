<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Advertisement;

/**
 * Class SuperController
 *
 */
class SuperController extends Controller
{
    const RESPONSE_STATUS_OK = 0;
    const RESPONSE_STATUS_WARNING = 1;
    const RESPONSE_STATUS_EXCEPTION = 2;

    const STATUS_ADVERTISEMENT =[
        'ACTIVE'=>1,
        'PENDING'=>2,
        'REJECT'=>3,
        'DEACTIVATED'=>4
    ];


    protected function setStatusAdvertisement(Advertisement $advertisement, int $status){
        $em = $this->getDoctrine()->getManager();
        $advertisement->setDirStatus($em->getRepository('AppBundle:DirStatus')->find($status));
        $em->persist($advertisement);
        $em->flush();
    }

}