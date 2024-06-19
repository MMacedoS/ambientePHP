<?php

class ProdutoController extends \Controller{
    use DateTrait;

    protected $produto_model;

    public function __construct() 
    {
        $this->validPainel(); 
        $this->produto_model = new ProdutoModel();      
    }

    public function getAll()
    {
        echo json_encode($this->produto_model->getAll());
    }
    public function getDadosProdutos($request = null)
    {
        echo  json_encode($this->produto_model->getProdutos($request));
    }

    public function buscaProduto($request =  '')
    {
        echo  json_encode($this->produto_model->getProdutos($request)['data']);
    }

    public function buscaProdutoPorId($id)
    {
        echo json_encode(self::messageWithData(201,'Dados encontrados', $this->produto_model->findById($id)));
    }

    public function salvarProdutos() {
        $produto = $this->produto_model->prepareInsertProduto($_POST);
        echo json_encode($produto);
    }

    public function atualizarProduto($id)
    {
        $produto = $this->produto_model->prepareUpdateProduto($_POST, $id);
        echo json_encode($produto);
    }

    public function changeStatusProduto($id)
    {
        $produto = $this->produto_model->prepareChangedProduto($id);
        echo json_encode($produto);
    }

    public function buscaEntradaProduto($request =  null)
    {
        return $this->produto_model->buscaEntradaProduto($request);
    }

    public function buscaProdutos()
    {       
        return $this->produto_model->buscaProdutos();
    }

    public function buscaEstoques($request = null)
    {       
        return $this->produto_model->buscaEstoques($request)['data'];
    }

    public function salvarEntrada()
    {
        echo json_encode($this->produto_model->salvarEntrada($_POST));
    }

    public function deleteEntrada($id)
    {
        $motivos = $_POST['motivo'];
        $dados = $this->produto_model->deleteEntrada($id, $motivos);
        echo json_encode($dados);
    }
}