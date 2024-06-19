<?php

class FuncionarioController extends \Controller{

    protected $funcionario_model;

    public function __construct() {
        $this->validPainel(); 
        $this->funcionario_model = new FuncionarioModel();      
    }

    public function getAll()
    {
        echo json_encode($this->funcionario_model->getAll());
    }

    public function buscaFuncionarios($request =  null)
    {
        echo json_encode($this->funcionario_model->findFuncionarios($request));
    }

    public function buscaFuncionarioPorId($id)
    {
        echo json_encode(self::messageWithData(201,'Dados encontrados', $this->funcionario_model->findById($id)));
    }

    public function salvarFuncionarios() {
        $funcionario = $this->funcionario_model->prepareInsertFuncionario($_POST);
        echo json_encode($funcionario);
    }

    public function atualizarFuncionarios($id)
    {
        $funcionario = $this->funcionario_model->prepareUpdateFuncionario($_POST, $id);
        echo json_encode($funcionario);
    }

    public function changeStatusFuncionarios($id)
    {
        $funcionario = $this->funcionario_model->prepareChangedFuncionario($id);
        echo json_encode($funcionario);
    }
}