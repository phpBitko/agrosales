<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('isEmpty', array($this, 'isEmptyCheck')),
        );
    }

    public function isEmptyCheck($text)
    {
        if(empty($text)){
            return 'Інформація відсутня.';
        }

        return $text;
    }
}