<?php

class Emailer {
    
    
    /**
    * Envia um e-mail utilizando o padrão do PHP
    * 
    * @param string $from_name nome do remetente
    * @param string $from_email endereço de e-mail do remetente
    * @param string $to endereço de e-mail do destinatário
    * @param string $subject assunto da mensagem enviada
    * @param string $content texto da mensagem enviada
    * 
    * @return boolean 
    */
    function send($from_name, $from_email, $to, $subject, $content){
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: $from_name<$from_email>" . "\r\n";
	return mail($to, utf8_decode($subject), utf8_decode($content), $headers);
    }
    
    
    
    /**
    * Envia um e-mail em formato html via servidor SMTP
    * 
    * @param string $from_name nome do remetente
    * @param string $from_email endereço de e-mail do remetente
    * @param string $to endereço de e-mail do destinatário
    * @param string $subject assunto da mensagem enviada
    * @param string $content texto da mensagem enviada
    * @param string $servidorSMTP URL ou IP do servidor SMTP a ser utilizado
    * @param string $porta porta do servidor SMTP
    * @param boolean $ssl se deve ser utilizado SSL na conexão com o servidor SMTP
    * @param string $username usuário para autenticação no servidor SMTP
    * @param string $password senha do usuário para autenticação no servidor SMTP
    * 
    * @return boolean 
    */
    function sendUsingSMTPServer($from_name, $from_email, $to, $subject, $content, $servidorSMTP, $porta, $ssl, $username, $password){

       //Necessário usar essa biblioteca para o uso de SMTP externo com SSL em ambiente sem PEAR.
       require_once($_SERVER['DOCUMENT_ROOT']."/sys/swiftmailer/lib/swift_required.php");

       if($ssl){
           $transport = Swift_SmtpTransport::newInstance($servidorSMTP, $porta, 'ssl')
               ->setUsername($username)
               ->setPassword($password);
       }
       else{
           $transport = Swift_SmtpTransport::newInstance($servidorSMTP, $porta)
               ->setUsername($username)
               ->setPassword($password);
       }

       $mailer = Swift_Mailer::newInstance($transport);

       $message = Swift_Message::newInstance($subject)
               ->setContentType("text/html")
               ->setCharset('utf-8')
               ->setFrom(array($from_email => $from_name))
               ->setTo(array($to))
               ->setReplyTo($from_email)
               ->setBody($content)
               ->setEncoder(Swift_Encoding::get8BitEncoding());

       if ($mailer->send($message)){
           return true;
       }
       else{
           return false;
       }
    }
}

?>