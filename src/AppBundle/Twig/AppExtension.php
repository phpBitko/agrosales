<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use AppBundle\Form\Helpers\FormErrorHelper;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('isEmpty', array($this, 'isEmptyCheck')),
            new TwigFilter('inArray', array($this, 'inArray')),
        );
    }

    public function isEmptyCheck($text)
    {
        if (empty($text)) {
            return 'Інформація відсутня!';
        }

        return $text;
    }

    public function inArray($array, $text)
    {
        return in_array($text, $array);
    }
}