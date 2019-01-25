<?php
namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class WarningException extends HttpException
{
    protected $statusCode;
    protected $message = "";

    public function __construct($message, $statusCode = 404)
    {
        $this->message=$message;
        $this->statusCode = $statusCode;
        parent::__construct($this->statusCode, $this->message, null, [], $this->statusCode);
    }


}