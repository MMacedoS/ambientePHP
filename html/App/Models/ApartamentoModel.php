<?php

require_once 'Trait/StandartTrait.php';
require_once 'Trait/FindTrait.php';

class ApartamentoModel extends ConexaoModel {

    use StandartTrait;
    use FindTrait;
    
    protected $conexao;

    protected $model = 'apartamento';

    public function __construct() 
    {
        $this->conexao = ConexaoModel::conexao();
    }

    public function prepareInsertApartamento($dados)
    {
        $validation = self::requiredParametros($dados);

        if(is_null($validation)){
            
            if($this->verificaApartamentoSeExiste($dados))
            {   
                return $this->insertApartamento($dados); 
            }

            return self::message(422, 'apartamento existente!');
        }

        return $validation;
    }

    private function verificaApartamentoSeExiste($dados)
    {
        $apartamento = (int)$dados['apartamento'];

        $cmd = $this->conexao->query(
            "SELECT 
                *
            FROM
                $this->model
            WHERE
                numero = $apartamento"
        );

        if($cmd->rowCount()>0)
        {
            return false;
        }

        return true;
    }

    private function insertApartamento($dados)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "INSERT INTO 
                    $this->model 
                SET 
                    numero = :apartamento, 
                    descricao = :descricao, 
                    tipo = :tipo,
                    status = :status"
                );

            $cmd->bindValue(':apartamento',$dados['apartamento']);
            $cmd->bindValue(':descricao',$dados['descricao']);
            $cmd->bindValue(':tipo',$dados['tipo']);
            $cmd->bindValue(':status',$dados['status']);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados inseridos!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function prepareUpdateApartamento($dados, $id)
    {
        $validation = self::requiredParametros($dados);

        if(is_null($validation)){            
            return $this->updateApartamento($dados, $id); 
        }

        return $validation;
    }

    private function updateApartamento($dados, int $id)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "UPDATE 
                    $this->model 
                SET 
                    numero = :apartamento, 
                    descricao = :descricao, 
                    tipo = :tipo,
                    status = :status
                WHERE 
                    id = :id"
                );

            $cmd->bindValue(':apartamento',$dados['apartamento']);
            $cmd->bindValue(':descricao',$dados['descricao']);
            $cmd->bindValue(':tipo',$dados['tipo']);
            $cmd->bindValue(':status',$dados['status']);
            $cmd->bindValue(':id',$id);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados Atualizados!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function getAll() {
        $cmd = $this->conexao->query(
            "SELECT 
                *
            FROM
                $this->model"
        );
        return $cmd->fetchAll();
    }

    public function findApartamentos($request)
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                * 
            FROM
                $this->model
            WHERE 
                numero
            LIKE
                '%$request%'"
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
        
    }

    public function prepareChangedApartamento($id, $dados)
    {
        $status = 1;
        $apartamento = self::findById($id);

        if(is_null($apartamento)) {
            return self::messageWithData(422, 'apartamento não encontrado', []);
        }

        $reserva = $this->conexao->query("SELECT count(*) as total FROM reserva WHERE apartamento_id = $id AND status = 3");   
        $reserva = $reserva->fetch();
        if($reserva['total'] > 0) {
            return "Existe ".$reserva['total'];
        }

        return $this->updateStatusApartamento($status, $id);
    }

    private function updateStatusApartamento($status, $id)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "UPDATE 
                    $this->model 
                SET 
                    status = :status
                WHERE 
                    id = :id"
                );
            $cmd->bindValue(':status',$status);
            $cmd->bindValue(':id',$id);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::messageWithData(200, "dados Atualizados!!", []);

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function prepareChangedApartamentoStatus($id, $status)
    {
        $apartamento = self::findById($id);

        if(is_null($apartamento)) {
            return self::messageWithData(422, 'apartamento não encontrado', []);
        }

        return $this->updateStatusApartamento($status, $id);
    }


    public function getApartamento()
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                * 
            FROM
                $this->model
            WHERE 
                status = 1"
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }
}