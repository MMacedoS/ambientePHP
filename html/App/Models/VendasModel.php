<?php

require_once 'Trait/StandartTrait.php';
require_once 'Trait/FindTrait.php';
require_once 'Trait/DateModelTrait.php';
require_once 'Trait/DataDropTrait.php';

class VendasModel extends ConexaoModel {

    use StandartTrait;
    use FindTrait;
    use DateModelTrait;
    use DataDropTrait;
    
    protected $model;
    protected $conexao;

    protected $table;

    public function __construct () {
        $this->table = "venda";
        $this->model = "venda";
        $this->conexao = ConexaoModel::conexao();
    }

    public function allVenda() {
        $con = $this->conexao->query(
            "SELECT *, 
                (
                    SELECT SUM(vi.quantidade * p.valor) AS valor_venda
                    FROM vendas_itens vi
                    INNER JOIN produto p ON vi.produto_id = p.id
                    WHERE vi.venda_id = v.id
                ) as valor_venda 
            FROM `venda` v;");
        
        return $con->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createVenda() {
        $vendas = $this->checkVendas();
        if(!empty($vendas)) {
            return $vendas;
        }
        
        $con = $this->conexao->prepare(
            "INSERT INTO venda set status ='aberta', funcionario = :funcionario, descricao = :descricao"
        );
        $con->bindValue(':funcionario',$_SESSION['code'], PDO::PARAM_INT);
        $con->bindValue(':descricao',"Consumidor não identificado");
        $con->execute();        
        return $this->checkVendas();
    }

    private function checkVendas() {
        $con = $this->conexao->query(
            "SELECT *
            FROM `venda` v where v.status ='aberta'");        
        return $con->fetch(); 
    }

    public function getVendasItemsByVendas($id) {
        $con = $this->conexao->query(
            "SELECT 
                p.descricao,
                p.valor,
                vi.quantidade,
                vi.id,
                vi.created_at
            FROM `vendas_itens` vi 
            INNER JOIN produto p on p.id = vi.produto_id where venda_id = '$id'");        
        return $con->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function getVendasItemsById($id) {
        $con = $this->conexao->query(
            "SELECT 
                id,
                quantidade
            FROM `vendas_itens` where id = '$id'");        
        return $con->fetch(PDO::FETCH_ASSOC); 
    }

    public function updateItensById($data) {
        if (empty($data)) {
            return null;
        }
        $con = $this->conexao->prepare("UPDATE `vendas_itens` SET quantidade = :quantidade WHERE id = :id");
        $con->bindValue(':quantidade',$data['quantidade']);
        $con->bindValue(':id',$data['swal_id_item']);
        return $con->execute();
    }

    public function addItensByIdVendas($data, $id) {
        if (empty($data)) {
            return null;
        }
        $con = $this->conexao->prepare("INSERT INTO `vendas_itens` SET produto_id = :produto, venda_id = :venda, status = :status, quantidade = :quantidade");
        $con->bindValue(':quantidade',$data['quantidade']);
        $con->bindValue(':venda',$data['id_venda']);
        $con->bindValue(':produto',$data['produto']);
        $con->bindValue(':status', 1);
        return $con->execute();
    }

    public function updateVendas($data, $id) {
        if (empty($data)) {
            return null;
        }
        $con = $this->conexao->prepare("UPDATE `venda` SET descricao = :descricao, valor = :valor, tipoPagamento = :tipo, status = :status WHERE id = :id");
        $con->bindValue(':descricao',$data['descricao']);
        $con->bindValue(':valor',$data['valor']);
        $con->bindValue(':tipo',$data['tipo']);
        $con->bindValue(':status',$data['status']);
        $con->bindValue(':id',$id);
        return $con->execute();
    }

    public function deleteItensById($id) 
    {             
        try {
            $con = $this->conexao->prepare("DELETE FROM `vendas_itens` WHERE id = :id");
            $con->bindValue(':id',$id);
            $con->execute();
            return true;

        } catch (\Throwable $th) {
            return false;
            //throw $th;
        }
    }

    public function deleteVendaById($id, $motivo)
    {  
            
        try {
            $this->conexao->beginTransaction();
            $dados = $this->findById($id);
            self::logError(json_encode($dados));
            $dados = $dados ?? null;

            if (is_null($dados)) {              
                $this->conexao->rollback();
                return null;
            }

            $this->prepareStatusTable('vendas_itens', 0," venda_id = $id");
            $this->prepareStatusTable('venda', 'cancelada', "id = $id");
                        
            $this->prepareStatusTable('entrada', 0," venda_id = $id");
            
            $apagadosModel = new ApagadosModel();        
            if(!$apagadosModel->insertApagados($dados, $motivo, 'venda', $id)) {
                $this->conexao->rollback();
                return null;
            };

            $this->conexao->commit();
            return true;
        } catch (\Throwable $th) {   
            $this->conexao->rollback();         
            self::logError($th->getMessage(). $th->getLine());
            return false;
        }
    }
}