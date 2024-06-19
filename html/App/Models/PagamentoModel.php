<?php

require_once 'Trait/StandartTrait.php';
require_once 'Trait/FindTrait.php';

class PagamentoModel extends ConexaoModel {

    use StandartTrait;
    use FindTrait;
    
    protected $conexao;

    protected $model = "pagamento";

    public function __construct() 
    {
        $this->conexao = ConexaoModel::conexao();
    }

    public function inserirPagamento($dados, $reserva_id)
    {
        $tipo = $dados['tipo'];
        $valor =  $dados['valor'];
        $descricao =  $dados['descricao'];
        $data = $dados['data'];

        if(empty($descricao))
        {
            $descricao = "Pagamento referente a reserva $reserva_id";
        }

        if(empty($data))
        {
            $data = Date('Y-m-d');
        }

        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "INSERT INTO 
                    $this->model 
                SET 
                    tipoPagamento =:tipo, 
                    valorPagamento = :valor, 
                    dataPagamento = :dataPagamento,
                    descricao = :descricao,
                    reserva_id = :reserva_id,
                    funcionario = :funcionario
                    "
                );

            $cmd->bindValue(':tipo',$tipo);
            $cmd->bindValue(':descricao',$descricao);
            $cmd->bindValue(':dataPagamento', $data);
            $cmd->bindValue(':valor',$valor);
            $cmd->bindValue(':reserva_id',$reserva_id);
            $cmd->bindValue(':funcionario',$_SESSION['code']);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados inseridos!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function getDadosPagamentos($id){
        $cmd  = $this->conexao->query(
            "SELECT 
                    *
                FROM 
                    $this->model
                WHERE 
                    reserva_id = $id
                AND status = 1
                order by id desc
            "
        );

        if($cmd->rowCount() > 0)
        {
            $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);
            return self::messageWithData(200, 'pagamentos encontrados', $dados);
        }

        return self::messageWithData(200, 'nenhum dado encontrado', []);
    }

    public function getRemovePagamento($id, $motivo = null){

        try {
            $this->conexao->beginTransaction();
            $dados = $this->findById($id);
            $dados = $dados ?? null;

            if (is_null($dados)) {              
                $this->conexao->rollback();
                return null;
            }

            $this->prepareStatusTable('pagamento', 0," id = $id");
                        
            $this->prepareStatusTable('entrada', 0," pagamento_id = $id");
            
            $apagadosModel = new ApagadosModel();        
            if(!$apagadosModel->insertApagados($dados, $motivo, 'pagamento', $id)) {
                $this->conexao->rollback();
                return null;
            };

            $this->conexao->commit();
            return true;
        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return $th->getMessage();
        }
    }
}