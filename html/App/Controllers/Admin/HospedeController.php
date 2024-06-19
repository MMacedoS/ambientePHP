<?php

class HospedeController extends \Controller{

    protected $hospede_model;

    public function __construct() {
        $this->validPainel(); 
        $this->hospede_model = new HospedeModel(); 
    }

    public function getAll() {
        echo json_encode($this->hospede_model->getAll());
    }

    public function getAllSelect() {
        echo json_encode($this->hospede_model->getAllOfSelect());
    }

    public function buscaHospedes($request =  null)
    {
        echo json_encode($this->hospede_model->findHospedes($request));
    }

    public function buscaHospedePorId($id)
    {
        echo json_encode(self::messageWithData(201,'Dados encontrados', $this->hospede_model->findById($id)));
    }

    public function salvarHospedes() {
        $hospede = $this->hospede_model->prepareInsertHospede($_POST);
        echo json_encode($hospede);
    }

    public function atualizarHospedes($id)
    {
        $hospede = $this->hospede_model->prepareUpdateHospede($_POST, $id);
        echo json_encode($hospede);
    }

    public function changeStatusHospedes($id)
    {
        $hospede = $this->hospede_model->prepareChangedHospede($id);
        echo json_encode($hospede);
    }

    
}