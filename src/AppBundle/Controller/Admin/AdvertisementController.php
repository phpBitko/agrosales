<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 15.08.2018
 * Time: 17:19
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Exception\WarningException;
use AppBundle\Service\PaginatorServices;
use Symfony\Component\EventDispatcher\Tests\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\AdvertisementType;
use AppBundle\Entity\Advertisement;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\Geometry;
use AppBundle\Filter\AdminAdvertisementFilterType;
use AppBundle\Controller\SuperController;


/**
 * Class CabinetController
 * @Route("/admin/advertisement")
 */
class AdvertisementController extends SuperController
{

    /**
     * @Route("/", name="admin.advertisement_index", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->redirectToRoute('admin_get_advertisement');
    }


    /**
     * @param Request $request
     * @param PaginatorServices $paginator
     * @return Response
     *
     * @Route("/getAdvertisement", name="admin.advertisement_get_advertisement", methods={"GET"})
     *
     */
    public function getAdvertisementAction(Request $request, PaginatorServices $paginator)
    {
        $advertisement = new Advertisement();
        $em = $this->getDoctrine()->getManager();
        $advertisementRepository = $em->getRepository('AppBundle:Advertisement');

        $formFilter = $this->createForm(AdminAdvertisementFilterType::class, null, ['entity_manager'=>$em]);

        if ($request->query->has($formFilter->getName())) {
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $advertisementRepository->qbFindByStatus();

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);
            $query = $filterBuilder->getQuery();
        }else{
            $query = $advertisementRepository->queryFindByStatus();
        }

        $pagination = $paginator->getPagination($query, $request->query->getInt('page', 1));

        return $this->render('AppBundle:admin/advertisement:get_advertisement.html.twig',
            array('advertisements' => $pagination, 'formFilter'=>$formFilter->createView()));
    }

    /**
     * @Route("/getUsers", name="admin_get_users", methods={"GET"})
     *
     */
    public function getUsers()
    {


        return $this->render('AppBundle:admin:get_users.html.twig');


    }

}