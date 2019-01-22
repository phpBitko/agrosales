<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Advertisement;
use AppBundle\Filter\AdvertisementFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Service\PaginatorServices;

/**
 * Class AdvertisementController
 * @Route("/advertisement")
 */
class AdvertisementController extends SuperController
{
    /**
     * @param Request $request
     * @param PaginatorServices $paginator
     * @param string $typeView
     *
     * @Route("/{typeView}",  defaults={"page": 1}, requirements={"typeView": "list|tab"}, name="advertisement_index", methods={"GET"})
     * @return Response
     */
    public function indexAction(Request $request, PaginatorServices $paginator, $typeView = 'list')
    {
        //$adv = new Advertisement();
        $em = $this->getDoctrine()->getManager();
        $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();

        $advertisement = $em->getRepository('AppBundle:Advertisement');

        $form = $this->createForm(AdvertisementFilterType::class, null, ['entity_manager' => $em]);

        if ($request->query->has($form->getName())) {           // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // initialize a query builder

            $filterBuilder = $advertisement->qbFindByStatus(self::STATUS_ADVERTISEMENT['ACTIVE']);

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
            $query = $filterBuilder->getQuery();
        } else {
            $query = $advertisement->queryFindByStatus(self::STATUS_ADVERTISEMENT['ACTIVE'], Advertisement::$order);
        }

        $pagination = $paginator->getPagination($query, $request->query->getInt('page', 1));
        $stringSelection = $this->identifySelectString($request);

        return $this->render('AppBundle:advertisement:index.html.twig', array(
            'form' => $form->createView(),
            'advertisement' => $pagination,
            'typeView' => $typeView,
            'stringSelection' => $stringSelection,

        ));
    }

    /**
     *
     * @param Advertisement $advertisement
     * @Route("/details/{id}", requirements={"id": "[1-9]\d*"}, name="advertisement_details", methods={"GET"})
     *
     * @return Response
     *
     */
    public function advertisementDetailsAction(Advertisement $advertisement)
    {
        if ($advertisement === null) {
            throw new NotFoundHttpException();
        }
        return $this->render('AppBundle:advertisement:details.html.twig', array('advertisement' => $advertisement));
    }

    /**
     * @param Advertisement $advertisement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/deactivateAdminAdvertisement/{id}", requirements={"id": "[1-9]\d*"}, name="advertisement_deactivate_admin_advertisement", methods={"GET"})
     *
     */
    public function deactivateAdvertisementAction(Advertisement $advertisement)
    {
        //Деактивувати може тільки адмін
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $this->setStatusAdvertisement($advertisement, self::STATUS_ADVERTISEMENT['DEACTIVATED']);

        $this->addFlash('success', 'Оголошення деактивовано.');
        return $this->redirectToRoute('advertisement_details', ['id' => $advertisement->getId()]);
    }

    /**
     * @param Advertisement $advertisement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/activateAdvertisement/{id}", requirements={"id": "[1-9]\d*"}, name="advertisement_activate_advertisement", methods={"GET"})
     *
     */
    public function activateAdvertisementAction(Advertisement $advertisement)
    {
        //Активувати може тільки адмін
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $this->setStatusAdvertisement($advertisement, self::STATUS_ADVERTISEMENT['ACTIVE']);

        $this->addFlash('success', 'Оголошення активовано.');
        return $this->redirectToRoute('advertisement_details', ['id' => $advertisement->getId()]);
    }

    /**
     * Формує строку для відображення типу сортування
     *
     * @param Request $request
     * @return string
     */
    protected function identifySelectString(Request $request)
    {
        $stringSelected = 'Сортувати';

        if ($request->query->get('sort')) {
            $field = explode('.', $request->query->get('sort'), 2);
            $ordertype = $request->query->get('direction');

            if ($field[1] == 'price') {
                $addString = ($ordertype == 'asc') ? ' дешевші' : ' дорожчі';
                $stringSelected = "Спочатку $addString";
            } elseif ($field[1] == 'area') {
                $addString = ($ordertype == 'asc') ? ' менші' : ' більші';
                $stringSelected = "Спочатку $addString за площею";
            } elseif ($field[1] == 'addDate') {
                $addString = ($ordertype == 'asc') ? ' раніше' : ' пізніше';
                $stringSelected = "Спочатку $addString додані";
            }
        }
        return $stringSelected;
    }


}
