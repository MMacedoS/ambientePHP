<?php

require_once __DIR__ . "/../Config/autoload.php";
require_once __DIR__ . "/../Trait/ErrorLoggingTrait.php";

class LoginController extends Controller {  
    use ErrorLoggingTrait;

    protected $login_model;
    protected $url;
    const SECRET_RECAPTCHA = '6LeV0jYpAAAAAK2AZx1VeRseq3xEUERh58-wgKDM';
    const URL_RECAPTCHA = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct() 
    {        
        $this->login_model = new LoginModel();
        $this->validLogin();
        $this->url = ROTA_GERAL;
    }

    public function findParamByParam($param) {
        $app_model = new AppModel();     
        return $app_model->buscaParamByParam($param)[0];
    }

    public function index() 
    {       
        require __DIR__ . '/../../View/Login/index.php';
    }

    private function validLogin() 
    {
        if (
            isset($_SESSION['acesso']) || 
            isset($_SESSION['logado'])
        ) {
            return $this->preparePainel();
        }
    }

    private function preparePainel() 
    {
        $painel_session =  $_SESSION['painel'];

        switch ($painel_session) {
           
            case 'Administrador':
                header('Location: '.$this->url.'/Administrativo');
            break;

            case 'Recepcao':
                header('Location: '.$this->url.'/Administrativo');
            break;
            // default: 
            //     header('Location: '.$this->url.'/Login');
            // break;            
        }
    }

    public function logar() 
    {
        $recaptcha = $this->validaRecaptcha($_POST['recaptcha']);
        if (!$recaptcha->success) {
            return;            
        }
        
       $login = $this->login_model->logar($_POST);
       if(!is_null(
            $login
            )
        ) {
            $_SESSION['code'] = $login['id'];
            $_SESSION['nome'] = $login['nome'];
            $_SESSION['painel'] = $login['painel'];
            $_SESSION['acesso'] = md5($login['painel']);
            $_SESSION['logado'] = true;  
            
            echo json_encode(true);          
        }

    }

    public function validaRecaptcha($data = null) 
    {
       $curl = curl_init();
       curl_setopt_array($curl, 
        [
           CURLOPT_URL => self::URL_RECAPTCHA,
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_CUSTOMREQUEST => 'POST',
           CURLOPT_POSTFIELDS => [
             'secret' => self::SECRET_RECAPTCHA,
             'response' => $data ?? ''
           ]
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function logouf() 
    {
        session_start();
        session_destroy();            
        return header('Location: '.$this->url.'/Login');    
    }


}