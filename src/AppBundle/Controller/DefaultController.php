<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Exception\FormException;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }
    
    /**
     * @Route("/contact_me", name="contact_me")
     */
    public function contactMeAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/contact_me.html.twig');
    }
    /**
     * @Route("/contact_me/submit", name="contact_me_submit")
     */
    public function submitAction(Request $request) {
        var_dump($_POST);
        $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $content = htmlspecialchars($_POST["content"]);
        $recaptcha = $_POST["g-recaptcha-response"];
        if (empty($recaptcha)) {
            $ex = new FormException("Missing recaptcha response.");
            $ex->setRedirect($this->generateUrl("contact_me"));
            throw $ex;
        }
        if (empty($name)) {
            $ex = new FormException("Missing sender name.");
            $ex->setRedirect($this->generateUrl("contact_me"));
            throw $ex;
        }
        if (empty($email)) {
            $ex = new FormException("Missing sender email.");
            $ex->setRedirect($this->generateUrl("contact_me"));
            throw $ex;
        }
        $secret = $this->getParameter("recaptcha_secret");
        $json = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha&remoteip=".$_SERVER["REMOTE_ADDR"]));
        if ($json->success) {
            $message = \Swift_Message::newInstance()
                    ->setSubject("Email from homepage")
                    ->setFrom($email)
                    ->setTo($this->getParameter("mailer_destination"))
                    ->setBody("From: $name\n"
                            . "Email: $email\n"
                            . "$content");
            $this->get('mailer')->send($message);
            return new RedirectResponse($this->generateUrl("homepage"));
        } else {
            $ex = new FormException("Recaptcha vaidation failed");
            $ex->setRedirect($this->generateUrl("contact_me"));
            throw $ex;
        }
        $this->setContent($json);
    }
}
