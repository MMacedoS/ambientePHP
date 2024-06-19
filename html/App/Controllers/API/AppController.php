<?php

require_once "./Config/autoload.php";

class AppController {  
    
    protected $reservas_controller;
    protected $url;

    public function __construct() 
    {        
        $this->reservas_controller = new ReservaController();
    }


    public function gerarDiarias()
    {
       $resposta = $this->reservas_controller->gerarDiarias();
      
       echo json_encode(
            ['status' => 200,
            'message' => 'Atualizado '. $resposta]
        );
        die;
    }

}