<?php
namespace AppBundle\ExceptionListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use AppBundle\Exception\FormException;

class FormExceptionListener {
    private $template;
    public function __construct($template) {
        $this->template = $template;
    }
    public function onKernelException(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();
        
        if ($exception instanceof FormException) {
            $response = new Response();
            $response->setContent($this->template->render('default/exception_handler.html.twig', array(
                "redirect" => $exception->getRedirect(),
                "error_msg" => $exception->getMessage()
            )));
            $event->setResponse($response);
        }
    }
}
