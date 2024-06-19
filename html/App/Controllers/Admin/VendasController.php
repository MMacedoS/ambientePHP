<?php

class VendasController extends \Controller{
    use DateTrait;

    protected $venda_model;

    public function __construct() 
    {
      $this->validPainel(); 
      $this->venda_model = new VendasModel();      
    }

    public function findAllVendas() { 
      echo json_encode($this->venda_model->allVenda());
    }

    public function addVenda()
    {
       $dados = $this->venda_model->createVenda();
       echo json_encode($dados);
    }

    public function getVendasItems($id) {
        $dados = $this->venda_model->getVendasItemsByVendas($id);
        echo json_encode($dados);
     }

     public function getItensPorId($id) {
        $dados = $this->venda_model->getVendasItemsById($id);
        echo json_encode($dados);
     }

     public function updateItensById() {
        $dados = $this->venda_model->updateItensById($_POST);
        echo json_encode($dados);
     }

     public function addItensByIdVenda($id) {
        $dados = $this->venda_model->addItensByIdVendas($_POST, $id);
        echo json_encode($dados);
     }

     public function deleteItensById($id) {
        $dados = $this->venda_model->deleteItensById($id);
        echo json_encode($dados);
     }

     public function updateVendas($id) {
        $dados = $this->venda_model->updateVendas($_POST, $id);
        echo json_encode($dados);
     }

     public function deleteVendas($id) {
         $motivos = $_POST['motivo'];
         $dados = $this->venda_model->deleteVendaById($id, $motivos);
        echo json_encode($dados);
     }
}