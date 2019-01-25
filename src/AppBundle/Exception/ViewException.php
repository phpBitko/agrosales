<?php
namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ViewException extends HttpException
{
    protected $statusCode;
    protected $message = "";

    public function __construct($message, $statusCode = 500)
    {
        $this->message=$message;
        $this->statusCode = $statusCode;
        parent::__construct($this->statusCode, $this->message, null, [], $this->statusCode);
    }


}