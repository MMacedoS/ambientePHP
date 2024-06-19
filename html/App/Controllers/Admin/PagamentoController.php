<?php

class PagamentoController extends \Controller{
    use DateTrait;

    protected $pagamento_model;

    public function __construct() 
    {
        $this->validPainel(); 
        $this->pagamento_model = new PagamentoModel();      
    }

    public function addPagamento($id)
    {
        $dados = $this->pagamento_model->inserirPagamento($_POST, $id);
        echo json_encode($dados);
    }

    public function getDadosPagamentos($code)
    {
        $reserva = $this->pagamento_model->getDadosPagamentos($code);
        echo json_encode($reserva);
    }

    public function getRemovePagamento($code)
    {        
        $motivos = $_POST['motivo'];
        $response = $this->pagamento_model->getRemovePagamento($code, $motivos);
        echo json_encode($response);
    }

}