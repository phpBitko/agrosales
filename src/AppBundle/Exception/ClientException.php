<?php
namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * Class WarningException
 * @package AppBundle\Exception
 * Використовується для виводу повідомлення на фронтенді із різними статусами
 *
 */
class ClientException extends HttpException
{
    protected $statusCode;
    protected $statusMessage;

    protected $message = "";

    public function __construct($message, $statusMessage = 1, $statusCode = 404)
    {
        $this->message=$message;
        $this->statusCode = $statusCode;
        $this->statusMessage = $statusMessage;

        parent::__construct($this->statusCode, $this->message, null, [], $this->statusCode);
    }

    /**
     * @return int
     */
    public function getStatusMessage(): int
    {
        return $this->statusMessage;
    }




}