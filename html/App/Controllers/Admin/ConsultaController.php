<?php

class ConsultaController extends Controller {

    private $reservaModel;
    
    public function __construct()
    {
        $this->validPainel(); 
        $this->reservaModel = new ReservaModel();
    }

    public function hospedadas(){
        echo json_encode(
            $this->reservaModel->buscaHospedadas('')
        );
    }

    public function checkin(){
        echo json_encode(
            $this->reservaModel->buscaCheckin('')
        );
    }

    public function checkout(){
        echo json_encode(
            $this->reservaModel->buscaCheckout('')
        );
    }

    public function confirmada(){
        echo json_encode(
            $this->reservaModel->buscaConfirmada('')
        );
    }

    public function reservada(){
        echo json_encode(
            $this->reservaModel->buscaReservadas('')
        );
    }

    public function mapa($params) {

        $params = explode("_@_", $params);

       $dados = $this->reservaModel->buscaMapaReservas($params[0], $params[1]);
        
        echo json_encode(
            $dados
        );
    }
}