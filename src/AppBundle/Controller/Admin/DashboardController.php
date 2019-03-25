<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Controller\SuperController;
use AppBundle\Service\StatisticData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CabinetController
 * @Route("/admin", name="admin.")
 */
class DashboardController extends SuperController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     */
    public function indexAction(Request $request, StatisticData $statisticData)
    {
        $data['countAdvertisement'] = $statisticData->getCountAdvertisement();

        return $this->render('AppBundle:admin/dashboard:index.html.twig', ['data'=>$data]);
    }

}