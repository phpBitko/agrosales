<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Advertisement;
use AppBundle\Entity\Messages;
use AppBundle\Entity\ViewInfo;
use AppBundle\Exception\ViewException;
use AppBundle\Filter\AdvertisementFilterType;
use AppBundle\Form\MessagesType;
use AppBundle\Service\ParseFilterServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Service\PaginatorServices;
use Symfony\Component\Config\Definition\Exception\Exception;


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
        $filterAttributes = '';
        $em = $this->em;

        $advertisement = $em->getRepository('AppBundle:Advertisement');
        $form = $this->createForm(AdvertisementFilterType::class, null, ['entity_manager' => $em]);

        $order = $this->getOrder($request);
        $query = $advertisement->queryFindByStatus(self::STATUS_ADVERTISEMENT['ACTIVE'], $order);

        if ($request->query->has($form->getName())) {          // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            if ($form->isValid()) {
                $filterBuilder = $advertisement->qbFindByStatus(self::STATUS_ADVERTISEMENT['ACTIVE'], $order);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
                $query = $filterBuilder->getQuery();
                $filterParams = $request->query->get($form->getName());
                $filterAttributes = $this->get('app.service.parse_filter')->parseQueryString($filterParams);

            } else {
                $this->addFlash('filter-danger', 'Помилка заповнення даних фільтру!');
            }
        }

        $pagination = $paginator->getPagination($query, $request->query->getInt('page', 1));
        $sortString = $this->parseSortString($request);

        $data = ['typeView' => $typeView, 'sortString' => $sortString, 'filterAttributes' => $filterAttributes];

        return $this->render('AppBundle:advertisement:index.html.twig', [
            'form' => $form->createView(),
            'advertisement' => $pagination,
            'data' => $data,
        ]);
    }


    /**
     * @param Request $request
     * @param Advertisement $advertisement
     *
     * @Route("/details/{id}", requirements={"id": "[1-9]\d*"}, name="advertisement_details", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function advertisementDetailsAction(Request $request, Advertisement $advertisement)
    {
        $formView = null;

        //Додаємо інформацію про перегляди
        $viewInfoRepository = $this->em->getRepository('AppBundle:ViewInfo');

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
            !$viewInfoRepository->findOneBy(['advertisement' => $advertisement, 'ip' => $request->getClientIp()])) {

            $viewInfo = new ViewInfo();

            $viewInfo->setIp($request->getClientIp())->setAdvertisement($advertisement);
            $this->em->persist($viewInfo);

            $advertisement->setViewCount($advertisement->getViewCount() + 1);
            $this->em->persist($advertisement);
            $this->em->flush();
        }
        if ($this->checkUserWithAuthorBool($advertisement) || $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $messages = new Messages();
            $form = $this->createForm(MessagesType::class, $messages);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                        throw $this->createAccessDeniedException();
                    }

                    $messages->setAdvertisement($advertisement);
                    $messages->setUsers($this->getUser());
                    $this->em->persist($messages);
                    $this->em->flush();

                    $this->rejectAdvertisement($advertisement);

                    return $this->redirectToRoute('advertisement_details',
                        ['id' => $advertisement->getId()]
                    );
                }
                $this->addFlash('danger', 'Перевірьте, будь ласка, правильність заповнення даних!');
            }

            if ($this->checkUserWithAuthorBool($advertisement)) {
                if (count($advertisement->getMessages()) > 0) {
                    foreach ($advertisement->getMessages() as $message) {
                        $message->setIsView(true);
                        $this->em->persist($message);
                    }
                    $this->em->flush();
                }
            }

            $formView = $form->createView();
        }

        return $this->render('AppBundle:advertisement:details.html.twig', array('advertisement' => $advertisement, 'messagesForm' => $formView));
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

    public function rejectAdvertisement(Advertisement $advertisement)
    {
        //Повернути на доопрацювання може тільки адмін
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $this->setStatusAdvertisement($advertisement, self::STATUS_ADVERTISEMENT['REJECT']);

        $this->addFlash('success', 'Оголошення повернуто на доопрацювання.');
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

        if (self::STATUS_ADVERTISEMENT['PENDING'] !== $advertisement->getDirStatus()->getId()) {
            throw new Exception('Активувати можна тільки повідомлення які знахdодяться на розгляді!', Response::HTTP_INTERNAL_SERVER_ERROR);
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
    protected function parseSortString(Request $request)
    {
        $stringSelected = 'Спочатку пізніше додані';

        if ($request->query->get('sort')) {
            $field = explode('.', $request->query->get('sort'), 2);
            $orderType = $request->query->get('direction');

            if ($field[1] == 'price') {
                $addString = ($orderType == 'asc') ? ' дешевші' : ' дорожчі';
                $stringSelected = "Спочатку$addString";
            } elseif ($field[1] == 'area') {
                $addString = ($orderType == 'asc') ? ' менші' : ' більші';
                $stringSelected = "Спочатку $addString за площею";
            } elseif ($field[1] == 'addDate') {
                $addString = ($orderType == 'asc') ? ' раніше' : ' пізніше';
                $stringSelected = "Спочатку $addString додані";
            }
        }
        return $stringSelected;
    }


}
