<?php

namespace AppBundle\Service\Admin;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class FormMapper
 * @package AppBundle\Service\Admin
 */
class FormMapper extends BaseMapper
{
    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    public function __construct(FormBuilderInterface $formBuilder)
    {
      $this->formBuilder = $formBuilder;
    }

    /**
     * @param $name
     * @param null $type
     * @param array $fieldOption
     * @return $this
     */
    public function add($name, $type = null, $fieldOption = [])
    {
        $this->formBuilder->add($name, $type, $fieldOption);

        $fieldOption['name'] = $name;

        $this->listFieldOptions[$name] = $fieldOption;

        return $this;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getFormBuilder(): FormBuilderInterface
    {
        return $this->formBuilder;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->formBuilder->getForm();
    }




}