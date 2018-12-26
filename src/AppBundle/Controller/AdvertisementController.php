<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Advertisement;
use AppBundle\Filter\AdvertisementFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityRepository;

/**
 * Class AdvertisementController
 * @Route("/advertisement")
 */
class AdvertisementController extends Controller
{

    /*Закоментовані строчки треба відразу удаляти. Вони якщо що вже є в GIT (Для вього проекту)
     * Іменування роутів. назва_контроллера_назва_екшена тобто  * @Route("/list/page/{page}", іменується advertisement_list_page
     * сам екшен тоді буде listPageAction. Це ще приклад поганий, але сисл такий. Назву контроллера в екшені не треба писать, якщо це просто назва а не смислова нагрузка.
     * * @Route("/getAllAdvertisement", name="get_all_advertisement", а мало б бути advertisement_get_all_advertisement
     * @Route("/details/{id}", requirements={"id": "[1-9]\d*"}, name="advertisement_details", тут екшен тоді має називатиcь detailsAction а не advertisementDetailsAction
     *
     *Добавити DOC блоки (Для вього проекту)
     *dump() поудаляти (Для вього проекту)
     *
     *
     * */


    /*Не вказаний метод для @Route("/page/{page}". за замовчуванням наче GET, але краще в явному вигляді*/
    /**
     * @Route("/",  defaults={"page": 1}, name="advertisement_index", methods={"GET"})
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="advertisement_index_paginated")
     *
     */
    public function indexAction(Request $request, $page = 1)
    {
        /* $file = __DIR__.'\..\Resources\public\18.json';
         dump(__DIR__.'\..\Resources\public\test.json');
         $stream = fopen($file, 'r');
         dump($stream);
         $listener = new \JsonStreamingParser\Listener\InMemoryListener();;
         try {
             $parser = new \JsonStreamingParser\Parser($stream, $listener);
             $parser->parse();
             fclose($stream);
         } catch (Exception $e) {
             fclose($stream);
             throw $e;
         }

         dump($listener->getJson());*/
        $adv = new Advertisement();
        $em = $this->getDoctrine()->getManager();
        $purpose = $em->getRepository('AppBundle:DirPurpose')->findAll();

        //Якщо не використовується, удаляєм
        $auxiliaryFunctionService = $this->get('app.service.auxiliary_function');
        $advertisement = $em->getRepository('AppBundle:Advertisement');

        /*        $form = $this->createForm(AdvertisementFilterType::class, $adv, array(
                    'entity_manager' => $em,
                ));*/

        $form = $this->createForm(AdvertisementFilterType::class, $adv, array(
            'purpose' => $purpose,

        ));
        dump($form);

        if ($request->query->has($form->getName())) {           // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // initialize a query builder
            $filterBuilder = $advertisement->queryLatestFilter();

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
            $query = $filterBuilder->getQuery();
        } else {
            $query = $advertisement->queryLatestFilter()->getQuery();
        }
        $repository = $advertisement->findLatestFilter($query, $page);

        return $this->render('AppBundle:advertisement:index.html.twig', array(
            'form' => $form->createView(),
            'advertisement' => $repository,

        ));

        // replace this example code with whatever you need

        //return $this->render('AppBundle:advertisement:index.html.twig', array('advertisement' => $advertisement3));
    }


    /*Перенести в indexAction*/
    /**
     * @Route("/list",  defaults={"page": 1}, name="advertisement_list", methods={"GET"})
     * @Route("/list/page/{page}", requirements={"page": "[1-9]\d*"}, name="advertisement_index_paginated_list")
     *
     */
    public function advertisementListAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $auxiliaryFunctionService = $this->get('app.service.auxiliary_function');
        $advertisement = $em->getRepository('AppBundle:Advertisement')->findLatest($page);
        // replace this example code with whatever you need
        return $this->render('AppBundle:advertisement:list.html.twig', array('advertisement' => $advertisement));
    }

    /**
     * @Route("/getAllAdvertisement", name="get_all_advertisement", options={"expose"=true}, methods={"POST"})
     *
     */
    public function getAllAdvertisementAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();

            /*Назва функції не інформативна. Там вибираються тільки активні, не всі Оголошення. а не Point, а Advertisement. Назва має говорити про тип обєкту який повертається
             зазвичай get використовуэться коли вибіраєш шась. тобто так наприклад - getActiveAdvertisement.
            Але функцію кораще зробити більш універсальну. Якщо тобі треба буде взяти не активні, або ті що на розгляді, то прийдеться ще раз таку функцію писати.
            тому робиму функцію, в якій передаємо параметр 1-2-3 і функція тоді буде називатись getAdvertisementByStatus або навіть getAdvertisementByIdStatus, а за замовчуванням потсавити 1(активні)
            */

            $advertisementPoints = $em->getRepository('AppBundle:Advertisement')->selectPoint();

            //Треба перевірити що сюди($advertisementPoints) вернулось, якщо пусто то буде помилка

            return $this->json(array('data' => $advertisementPoints), Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->json(array('error' => $exception->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/details/{id}", requirements={"id": "[1-9]\d*"}, name="advertisement_details", methods={"GET"})
     *
     */
    public function advertisementDetailsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $advertisementDetails = $em->getRepository('AppBundle:Advertisement')->findOneBy(array('id' => $id));

        if ($advertisementDetails === null) {
            throw new NotFoundHttpException();
        }
        return $this->render('AppBundle:advertisement:details.html.twig', array('advertisement' => $advertisementDetails));
//            if ($advertisementDetails->isActive()== true) {
//                return $this->render('AppBundle:advertisement:details.html.twig', array('advertisement' => $advertisementDetails));
//            } else {
//                return $this->redirectToRoute('get_all_advertisement');
//            }
    }



}
