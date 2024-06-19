<?php

class ConsumoController extends \Controller{
    use DateTrait;

    protected $consumo_model;

    public function __construct() {
        $this->validPainel(); 
        $this->consumo_model = new ConsumoModel();      
    }

    public function addConsumo($id)
    {
        $dados = $this->consumo_model->inserirConsumo($_POST, $id);
        echo json_encode($dados);
    }

    public function updateConsumo($id)
    {
        $dados = $this->consumo_model->updateConsumo($_POST, $id);
        echo json_encode($dados);
    }

    public function getConsumoPorId($id)
    {
        $consumo = self::messageWithData(201,'Dados encontrados', $this->consumo_model->findById($id));
        echo json_encode($consumo);
    }

    public function getDadosConsumos($code)
    {
        $consumo = $this->consumo_model->getDadosConsumos($code);
        echo json_encode($consumo);
    }

    public function getRemoveConsumo($code)
    {
        $motivos = $_POST['motivo'];
        $response = $this->consumo_model->getRemoveConsumo($code, $motivos);
        echo json_encode($response);
    }

}