<?php

namespace AppBundle\Twig;

use Twig\TwigFilter;
use Twig\TwigTest;

class TwigTypeTestExtension extends \Twig_Extension
{
    protected $env;

    public function getName()
    {
        return 'Twig Type Test';
    }

    public function getTests()
    {
        return array(new TwigTest( 'of_type', [$this, 'twig_of_type']));
    }

    public function getFilters()
    {
        return array(new TwigFilter( 'get_type', [$this, 'twig_get_type']));
    }

    public function initRuntime(\Twig_Environment $env)
    {
        $this->env = $env;
    }

    public function twig_of_type($var, $typeTest = null, $className = null)
    {

        switch ($typeTest) {
            default:
                return false;
                break;

            case 'date':
                return $var instanceof \DateTime;
                break;

            case 'array':
                return is_array($var);
                break;

            case 'bool':
                return is_bool($var);
                break;

            case 'class':
                return is_object($var) === true && get_class($var) === $className;
                break;

            case 'float':
                return is_float($var);
                break;

            case 'int':
                return is_int($var);
                break;

            case 'numeric':
                return is_numeric($var);
                break;

            case 'object':
                return is_object($var);
                break;

            case 'scalar':
                return is_scalar($var);
                break;

            case 'string':
                return is_string($var);
                break;
        }
    }

    public function twig_get_type($var)
    {
        return gettype($var);
    }

}