<?php
namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class WarningException extends HttpException
{
    protected $statusCode = 404;
    protected $message = "";

    public function __construct($message)
    {
        $this->message=$message;
        parent::__construct($this->statusCode, $this->message);
    }


}