<?php

class ApagadosController extends \Controller{
    use DateTrait;

    protected $apagado_model;

    public function __construct() 
    {
      $this->validPainel(); 
      $this->apagado_model = new ApagadosModel();      
    }

    public function findAllApagados() { 
      echo json_encode($this->apagado_model->getApagados());
    }

    public function changeStatusApagados($id) 
    {
      echo json_encode($this->apagado_model->updateApagadosStatus($id));
    }

    public function findById($id) {
      echo json_encode($this->apagado_model->findByIdWithFuncionario($id));
    }

    public function changeAllStatusApagados() 
    {
      echo json_encode($this->apagado_model->changeAllStatusApagados());
    }
}