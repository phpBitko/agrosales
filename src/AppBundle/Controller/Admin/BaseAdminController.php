<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Service\Admin\Interfaces\ListAdminInterface;
use AppBundle\Service\Admin\ViewMapper;
use AppBundle\Service\Admin\FormMapper;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\PaginatorServices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 *
 * Class BaseController
 * @Route("/admin", name="admin.")
 */
class BaseAdminController extends Controller
{


    /**
     * @param Request $request
     * @param ViewMapper $viewMapper
     * @param $id
     * @return Response
     * @throws \ReflectionException
     *
     * @Route ("/{entity}/{id}/view", name="object_view", methods={"GET"})
     */
    public function viewAction(Request $request, ViewMapper $viewMapper, $id)
    {
        $path = explode('/', $request->getPathInfo());
        //Отримуємо назву табилці
        $table = array_reverse($path)[2];


        $objectConfigFields = $this->getConfigFieldsObject($table);
        $objectConfigFields->configureViewFields($viewMapper);

        $listField = $viewMapper->preparationFieldForQuery($viewMapper->getListFieldName());

        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository('AppBundle:' . ucfirst($table))->findByIdOnlyCustomColumn($id, $listField);
        if (empty($obj)) {
            throw new NotFoundHttpException();
        }

        return $this->render('AppBundle:admin/template/view:body_template.html.twig',
            [
                'options' => $objectConfigFields->getOptions(),
                'fieldOptions' => $viewMapper->getListFieldOptions(),
                'data' => $obj[0]
            ]);
    }


    /**
     * @param Request $request
     * @param string $entity
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \ReflectionException
     *
     * @Route ("/{entity}/create", name="object_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, string $entity, UserPasswordEncoderInterface $passwordEncoder)
    {
        $class = new \ReflectionClass('\AppBundle\Entity\\' . ucfirst($entity));
        $obj = $class->newInstance();

        $objectConfigFields = $this->container->get('AppBundle\Service\Admin\ConfigFields\\'. ucfirst($entity).'Admin');

        //Співставляємо дані з об'єкту з білдером. При створенні об'єкту додаткова валідація з групою 'Create'
        $builder = $this->createFormBuilder($obj, ['validation_groups'=>['Default', 'Create']]);

        $formMapper = new FormMapper($builder);        //Конфігуруємо форму
        $objectConfigFields->configureFormFields($formMapper);

        //Отримуємо результуючу форму
        $form = $formMapper->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $obj = $form->getData();

            if($obj instanceof User){
                $password = $passwordEncoder->encodePassword($obj, $obj->getPlainPassword());
                $obj->setPassword($password);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($obj);
            $em->flush();

            $this->addFlash('success', 'Дані успішно збережені!');
            return $this->redirect($request->getUri());
        }

        return $this->render('AppBundle:admin/template/form:body_template.html.twig',
            [
                'options' => $objectConfigFields->getOptions(),
                'fieldOptions' => $formMapper->getListFieldOptions(),
                'form' => $form->createView()
            ]);
    }


    /**
     * @param Request $request
     * @param $id
     * @param string $entity
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @Route ("/{entity}/{id}/edit", name="object_edit", methods={"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request, $id, string $entity, UserPasswordEncoderInterface $passwordEncoder)
    {
        $objectConfigFields = $this->container->get('AppBundle\Service\Admin\ConfigFields\\'.ucfirst($entity).'Admin');

        //Отримуємо об'єкт для редагування
        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository('AppBundle:' . ucfirst($entity))->find($id);

        if($obj instanceof UserInterface){
            $password = $obj->getPassword();
        }

        //Співставляємо дані з об'єкту з білдером
        $builder = $this->createFormBuilder($obj);

        $formMapper = new FormMapper($builder);

        //Конфігуруємо форму
        $objectConfigFields->configureFormFields($formMapper);

        //Отримуємо результуючу форму
        $form = $formMapper->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $obj = $form->getData();
            //Якщо об'єкт користувача, шифруємо пароль, якщо він був введений
            if($obj instanceof UserInterface){
                if(!empty($obj->getPlainPassword())){
                    $password = $passwordEncoder->encodePassword($obj, $obj->getPlainPassword());
                }
                $obj->setPassword($password);
            }
            $em->persist($obj);
            $em->flush();

            $this->addFlash('success', 'Дані успішно збережені!');
            return $this->redirect($request->getUri());
        }

        return $this->render('AppBundle:admin/template/form:body_template.html.twig',
            [
                'options' => $objectConfigFields->getOptions(),
                'fieldOptions' => $formMapper->getListFieldOptions(),
                'form' => $form->createView()
            ]);
    }


    /**
     * Повертає об'єкт з простору імен \AppBundle\Service\Admin\ConfigFields по назві класу
     *
     * @param $table
     * @return object
     * @throws \ReflectionException
     */
    protected function getConfigFieldsObject($table)
    {
        $em = $this->getDoctrine()->getManager();
        $class = new \ReflectionClass('\AppBundle\Service\Admin\ConfigFields\\' . ucfirst($table) . 'Admin');
        $obj = $class->newInstance($em);
        return $obj;
    }


    /**
     * @param Request $request
     * @param ListAdminInterface $listAdmin
     * @param PaginatorServices $paginator
     * @return Response
     * @throws \Exception
     */
    protected function listAction(Request $request, ListAdminInterface $listAdmin, PaginatorServices $paginator): Response
    {
        //Отримуєм квері для пагінатора
        $query = $listAdmin->getListQuery();

        if (empty($query)) {
            throw new \Exception('Помилка формування запиту до бази даних');
        }

        //Отримуємо загальні налаштування для пагінатора, і рендера сторінки
        $options = $listAdmin->getOptions();
        $pagination = $paginator->getPagination($query, $request->query->getInt('page', 1), $options['num_items_by_page']);

        return $this->render('AppBundle:admin/template/list:body_template.html.twig',
            [
                'options' => $listAdmin->getResolver()->resolve(),
                'fieldOptions' => $listAdmin->getListMapper()->getListFieldOptions(),
                'data' => $pagination
            ]);
    }


}