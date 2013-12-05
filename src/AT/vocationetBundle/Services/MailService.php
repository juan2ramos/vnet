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
     * @param string|array $email correo o lista de correos
     * @param string $subject asunto
     * @param array $data arreglo de datos para la vista del email
     * @param string $delivery_type tipo de entrega to|cc|bcc
     */
    public function sendMail($email, $subject, $data, $delivery_type = 'to')
    {
        $mail = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('no_reply@altactic.com', 'Vocationet')
            ->setBody($this->templating->render('::mail.html.twig', $data), 'text/html');
        
        if($delivery_type == 'to')
        {
            $mail->setTo($email);
        }
        elseif($delivery_type == 'cc')
        {
            $mail->setCc($email);
        }
        elseif($delivery_type == 'bcc')
        {
            $mail->setBcc($email);
        }
        
        $this->mailer->send($mail);
    }
}
?>
