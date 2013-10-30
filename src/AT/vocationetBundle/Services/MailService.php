<?php

namespace AT\vocationetBundle\Services;

class MailService
{
    protected $mailer;
    protected $templating; 
    
    function __construct($mailer, $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }
    
    /**
     * Funcion para enviar un correo
     * 
     * @param string|array $email correo o lista de correos emisores
     * @param string $subject asunto
     * @param array $data arreglo de datos para la vista del email
     */
    public function sendMail($email, $subject, $data)
    {
        $mail = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('no_reply@vocationet.com', 'Vocationet')
            ->setTo($email)
            ->setBody($this->templating->render('::mail.html.twig', $data), 'text/html');
        
        $this->mailer->send($mail);
    }
}
?>
