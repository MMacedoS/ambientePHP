<?php

class FinanceiroController extends \Controller{
    use DateTrait;

    protected $financeiro_model;
    protected $entrada_model;
    protected $saida_model;

    public function __construct() {
        $this->validPainel(); 
        $this->financeiro_model = new FinanceiroModel(); 
        $this->entrada_model = new EntradaModel();   
        $this->saida_model = new SaidaModel();      
    }
    
    public function buscaMovimentos($request = null)
    {       
        return $this->financeiro_model->buscaMovimentos($request);
    }

    public function buscaEntrada($request = null)
    {     
        return $this->entrada_model->buscaEntrada($request);///old formula
    }

    public function findAllEntradas($request = null)
    {       
        echo json_encode($this->entrada_model->buscaEntrada($request));
        return;
    }

    public function findEntradasByParams()
    {       
        echo json_encode($this->entrada_model->buscaEntradaByParams($_POST));
        return;
    }

    public function findEntradaById($id)
    {       
        echo json_encode(self::messageWithData(201,'Dados encontrados', $this->entrada_model->findById($id)));
        return;
    }

    public function deleteEntradaById($id)
    {
        $motivos = $_POST['motivo'];
        $response = $this->entrada_model->deleteById($id, $motivos);
        echo json_encode($response);
        return;
    }

    public function buscaSaida($request = null)
    {       
        return $this->financeiro_model->buscaSaida($request);
    }

    public function findAllSaidas($request = null)
    {       
        return;
    }

    public function insertSaida()
    {
        echo json_encode($this->saida_model->insertSaida($_POST));
    }

    public function findSaidasByParams()
    {       
        echo json_encode($this->saida_model->buscaSaidasByParams($_POST));
        return;
    }

    public function salvarEntradas()
    {
        echo json_encode($this->entrada_model->insertEntrada($_POST));
    }

    public function deleteSaidaById($id)
    {
        $motivos = $_POST['motivo'];
        $response = $this->saida_model->deleteById($id, $motivos);
        echo json_encode($response);
        return;
    }

    public function atualizarEntradas($id)
    {
        echo json_encode($this->entrada_model->updateEntrada($_POST, $id));
    }

    public function findMovimentosByParams() {
        echo json_encode($this->financeiro_model->findMovimentosByParams(
            $_POST['description'], 
            $_POST['startDate'], 
            $_POST['endDate'], 
            $_POST['status']
            )
        );
    }

    public function findMovimentos() {
        echo json_encode($this->financeiro_model->findMovimentosByParams());
    }

    public function findAllPagamento() {
        echo json_encode($this->financeiro_model->findPagamentosByParams());
    }

    public function findPagamentoByParams() {
        echo json_encode($this->financeiro_model->findPagamentosByParams(
            $_POST['description'], 
            $_POST['startDate'], 
            $_POST['endDate'],
            $_POST['funcionarios']
            )
        );
    }
}