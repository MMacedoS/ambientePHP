<?php

require_once 'Trait/StandartTrait.php';
require_once 'Trait/FindTrait.php';
require_once 'Trait/DateModelTrait.php';
require_once 'Trait/DataDropTrait.php';

class EntradaModel extends ConexaoModel {

    use StandartTrait;
    use FindTrait;
    use DateModelTrait;
    use DataDropTrait;
    
    protected $conexao;

    protected $model = 'entrada';
    protected $user = '';

    public function __construct() 
    {
        $this->model = 'entrada';
        $this->user = $_SESSION['code'];
        $this->conexao = ConexaoModel::conexao();
    }

    public function buscaEntrada($off = 0)
    {
        if(is_null($off) || empty($off))
        {
            return $this->entrada($off = 0);
        } 
        $dados = explode('_@_', $off);   

        if(count($dados) == 1)
        {
            $chave = str_replace('page=', '', $dados[0]);
            return $this->entrada($chave);
        }
        return true;
    }

    public function buscaEntradaByParams($params)
    {
        if(is_null($params) || empty($params))
        {
            return $this->entrada($off = 0);
        } 

        $funcionario = $params['funcionarios'] == 'todos' ? null : $params['funcionarios'];   
        
        if(!is_null($funcionario)) {
            $funcionario = "AND e.funcionario = '$funcionario'";
        }

        if($_SESSION['painel'] == 'Recepcao') {
            $funcionario = "AND e.funcionario = '$this->user'";
        }

        return $this->entradaComParams(
            $params['search'],
            $params['startDate'], 
            $params['endDate'],
            $params['status'],
            $funcionario
        );
    }

    public function entrada($off = 0)
    {         
        $cmd  = $this->conexao->query(
            "SELECT 
                e.id,
                e.descricao,
                e.tipoPagamento,
                r.id as reserva_code,
                h.nome as Hospede,
                a.numero as apt,
                e.created_at,
                e.pagamento_id,
                e.valor 
            FROM 
                $this->model e
            LEFT JOIN pagamento p on p.id = e.pagamento_id
            LEFT JOIN reserva r on p.reserva_id = r.id
            LEFT JOIN hospede h on h.id = r.hospede_id
            LEFT JOIN apartamento a on a.id = r.apartamento_id
            WHERE 
                e.funcionario = '$this->user'            
               AND e.status = 1
            ORDER BY
                e.created_at DESC
            LIMIT 12 offset $off "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public function entradaComParams($texto = 0, $entrada, $saida, $tipoPagamento, $funcionario)
    {
        $entrada = date($entrada . ' 00:00:00');
        $saida = date($saida . '  23:59:59');
       
        $sql  = "SELECT 
            e.id,
                e.descricao,
                e.tipoPagamento,
                r.id as reserva_code,
                h.nome as Hospede,
                a.numero as apt,
                e.created_at,
                e.pagamento_id,
                e.valor  
        FROM 
            $this->model e
        LEFT JOIN pagamento p on p.id = e.pagamento_id
        LEFT JOIN reserva r on p.reserva_id = r.id
        LEFT JOIN hospede h on h.id = r.hospede_id
        LEFT JOIN apartamento a on a.id = r.apartamento_id
        WHERE
            e.created_at BETWEEN '$entrada' AND '$saida'
        AND (('$tipoPagamento' = '' or '$tipoPagamento' is null) or e.tipoPagamento = '$tipoPagamento')
        AND e.descricao LIKE '%$texto%' 
        AND e.status = 1 
        $funcionario
        ORDER BY
                e.created_at ASC
        ";

        self::logError($sql);

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public function insertEntrada($dados)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "INSERT INTO 
                    $this->model
                SET 
                    valor = :valor, 
                    descricao = :descricao, 
                    tipoPagamento = :tipopagamento,
                    funcionario = :funcionario"
                );

            $cmd->bindValue(':valor',$dados['valor']);
            $cmd->bindValue(':descricao',$dados['descricao']);
            $cmd->bindValue(':tipopagamento',$dados['pagamento']);
            $cmd->bindValue(':funcionario',$this->user);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados inseridos!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function updateEntrada($dados, $id)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "UPDATE 
                    $this->model
                SET 
                    valor = :valor, 
                    descricao = :descricao, 
                    tipoPagamento = :tipopagamento,
                    funcionario = :funcionario
                WHERE 
                    id = :id"
                );

            $cmd->bindValue(':valor',$dados['valor']);
            $cmd->bindValue(':descricao',$dados['descricao']);
            $cmd->bindValue(':tipopagamento',$dados['pagamento']);
            $cmd->bindValue(':funcionario',$this->user);
            $cmd->bindValue(':id',$id);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados atualizados!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
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

            $this->prepareStatusTable('entrada', 0," id = $id");
            
            $apagadosModel = new ApagadosModel();        
            if(!$apagadosModel->insertApagados($dados, $motivo, 'entrada', $id)) {
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