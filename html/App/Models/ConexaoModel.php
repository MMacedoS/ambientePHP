<?php
require_once __DIR__ . "/../Control/Conexao.php";
require_once __DIR__ . "/../Trait/ErrorLoggingTrait.php";
class ConexaoModel
{
    use ErrorLoggingTrait;
   public function conexao()
    {
        $con = new Conexao;
        return $con->MontarConexao();        
    }

    public function enviarEmail($email_usu,$txt_subject ,$text_message)
    {
        
        //AQUI VAI O CÓDIGO DE ENVIO DO EMAIL
        try{
            $to = $email_usu;
            $subject = $txt_subject;
            $email_adm="contato@escolaisttucano.com.br";
            $message = $text_message;
    
        $remetente = $email_adm;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8;' . "\r\n";
        $headers .= "From: " .$remetente;
        mail($to, $subject, $message, $headers);
            return true;
        }catch(Exception $th)
        {
            return false;
        }
    }

}