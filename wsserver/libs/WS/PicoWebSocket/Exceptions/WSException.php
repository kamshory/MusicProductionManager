<?php
namespace WS\PicoWebSocket\Exceptions;

use Exception;
use Throwable;

class WSException extends Exception
{
    /**
     * Previous exception
     *
     * @var Throwable
     */
    private $previous;

    /**
     * @param string $message Exception message
     * @param mixed $code Exception code
     * @param Throwable $previous Previous exception
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if (!is_null($previous)) {
            $this->previous = $previous;
        }
    }
}