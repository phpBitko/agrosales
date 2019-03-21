<?php

namespace AppBundle\Service\Admin\Interfaces;

use AppBundle\Service\Admin\ListMapper;

interface ListAdminInterface
{
    public function configureListFields(ListMapper $listMapper);
}