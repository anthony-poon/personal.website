<?php
namespace AppBundle\Exception;

class FormException extends \Exception {
    //put your code here
    private $redirectUrl;
    function __construct(string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    function setRedirect(String $redirectUrl) {
        $this->redirectUrl = $redirectUrl;
    }
    
    function getRedirect() {
        return $this->redirectUrl;
    }
}
