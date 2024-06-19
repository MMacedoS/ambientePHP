<?php

class DiariaController extends \Controller{
    use DateTrait;

    protected $diarias_model;

    public function __construct() {
        $this->validPainel(); 
        $this->diarias_model = new DiariasModel();      
    }

    public function addDiaria($id)
    {
        $dados = $this->diarias_model->inserirDiaria($_POST, $id);
        echo json_encode($dados);
    }

    public function getDiariaPorId($id)
    {
        $consumo = self::messageWithData(201,'Dados encontrados', $this->diarias_model->findById($id));
        echo json_encode($consumo);
    }    

    public function getDadosDiarias($code)
    {
        $reserva = $this->diarias_model->getDadosDiarias($code);
        echo json_encode($reserva);
    }

    public function getDiariasPorId($id)
    {
        $diarias = $this->diarias_model->findDiariasById($id);
        echo json_encode($diarias);
    }

    public function updateDiaria($id)
    {
        $diarias = $this->diarias_model->updateDiarias($_POST, $id);
        echo json_encode($diarias);
    }

    public function getRemoveDiarias($id)
    {        
        $motivos = $_POST['motivo'];
        $response = $this->diarias_model->getRemoveDiarias($id, $motivos);
        echo json_encode($response);
    }  

}