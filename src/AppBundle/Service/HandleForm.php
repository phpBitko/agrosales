<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class HandleForm extends BaseEmServices
{

    public function  baseHandleForm(FormInterface $form, Request $request)
    {
        if(!$request->isMethod('POST')){
            return false;
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return false;
        }

        return true;
    }



}