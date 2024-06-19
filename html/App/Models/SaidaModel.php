<?php

require_once 'Trait/StandartTrait.php';
require_once 'Trait/FindTrait.php';
require_once 'Trait/DateModelTrait.php';
require_once 'Trait/DataDropTrait.php';

class SaidaModel extends ConexaoModel {

    use StandartTrait;
    use FindTrait;
    use DateModelTrait;
    use DataDropTrait;
    
    protected $conexao;

    protected $model = 'saida';
    protected $user = null;

    public function __construct() 
    {
        $this->model = 'saida';
        $this->conexao = ConexaoModel::conexao();
        $this->user = $_SESSION['code'];
    }

    // public function buscaSaida($off = 0)
    // {
    //     if(is_null($off) || empty($off))
    //     {
    //         return $this->saida($off = 0);
    //     } 
    //     $dados = explode('_@_', $off);   

    //     if(count($dados) == 1)
    //     {
    //         $chave = str_replace('page=', '', $dados[0]);
    //         return $this->saida($chave);
    //     }

    //     return $this->saidaComParams(
    //         $dados[1],
    //         $dados[2], 
    //         $dados[3]
    //     );
    // }

    public function saida($off = 0)
    {
       
        $cmd  = $this->conexao->query(
            "SELECT 
                id,
                descricao,
                tipoPagamento,
                created_at,
                valor 
            FROM
                $this->model 
            WHERE 
                funcionario = '$this->user'                             
               AND status = 1
            ORDER BY
                id DESC
            LIMIT 12 offset $off "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function saidaComParams($texto = 0, $entrada, $saida, $tipo, $funcionario)
    {
        $entrada = date($entrada . ' 00:00:00');
        $saida = date($saida . ' 23:59:59');
       
        $sql  = "SELECT 
                    id,
                    descricao,
                    tipoPagamento,
                    created_at,
                    valor 
                FROM
                    $this->model
                WHERE
                    created_at between '$entrada' and '$saida'  
                    and status = 1 
                    $funcionario
                ";

        if(!empty($texto)){
                
            $sql.= "
            AND
               descricao = $texto                 
            ";
        }

        if(!empty($tipo)){
                
            $sql.= "
            AND
               tipoPagamento = $tipo                 
            ";
        }

        $sql.= "
            ORDER BY id desc               
        ";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function insertSaida($dados)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "INSERT INTO 
                    saida
                SET 
                    valor = :valor, 
                    descricao = :descricao, 
                    tipoPagamento = :tipopagamento,
                    tipo = :tipo,
                    funcionario = :funcionario"
                );

            $cmd->bindValue(':valor',$dados['valor']);
            $cmd->bindValue(':descricao',$dados['descricao']);
            $cmd->bindValue(':tipo',$dados['tipo']);
            $cmd->bindValue(':tipopagamento',$dados['pagamento']);
            $cmd->bindValue(':funcionario',$_SESSION['code']);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados inseridos!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function buscaSaidasByParams($params)
    {
        if(is_null($params) || empty($params))
        {
            return $this->saida($off = 0);
        } 

        $funcionario = $params['funcionarios'] == 'todos' ? null : $params['funcionarios'];   
        
        if(!is_null($funcionario)) {
            $funcionario = "AND funcionario = '$funcionario'";
        }

        if($_SESSION['painel'] == 'Recepcao') {
            $funcionario = "AND funcionario = '$this->user'";
        }

        return $this->saidaComParams(
            $params['search'],
            $params['startDate'], 
            $params['endDate'],
            $params['status'], 
            $funcionario            
        );
    }

    public function deleteById($id, $motivo)
    {
        if(is_null($id)) {
            return null;
        }
        
        try {
            $this->conexao->beginTransaction();
            $dados = $this->findById($id);
            $dados = $dados ?? null;

            if (is_null($dados)) {              
                $this->conexao->rollback();
                return null;
            }

            $this->prepareStatusTable('saida', 0," id = $id");
            
            $apagadosModel = new ApagadosModel();        
            if(!$apagadosModel->insertApagados($dados, $motivo, 'saida', $id)) {
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