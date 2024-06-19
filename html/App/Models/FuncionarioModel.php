<?php

require_once 'Trait/StandartTrait.php';
require_once 'Trait/FindTrait.php';

class FuncionarioModel extends ConexaoModel {

    use StandartTrait;
    use FindTrait;
    
    protected $conexao;

    protected $model = 'usuarios';

    public function __construct() 
    {
        $this->conexao = ConexaoModel::conexao();
    }

    public function prepareInsertFuncionario($dados)
    {
        $validation = self::requiredParametros($dados);

        if(is_null($validation)){
            
            if($this->verificaFuncionarioSeExiste($dados))
            {   
                return $this->insertFuncionario($dados); 
            }

            return self::message(422, 'Funcionario existente!');
        }

        return $validation;
    }

    private function verificaFuncionarioSeExiste($dados)
    {
        $email = (string)$dados['email'];
        
        $cmd = $this->conexao->query(
            "SELECT 
                *
            FROM
                $this->model
            WHERE
                email = '$email'"
        );

        if($cmd->rowCount()>0)
        {
            return false;
        }

        return true;
    }

    private function insertFuncionario($dados)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "INSERT INTO 
                    $this->model 
                SET 
                    nome = :nome, 
                    email = :email, 
                    senha = :senha,
                    status = :status,
                    painel = :painel
                    "
                );

            $cmd->bindValue(':nome',$dados['nome']);
            $cmd->bindValue(':email',$dados['email']);
            $cmd->bindValue(':status',$dados['status']);
            $cmd->bindValue(':painel',$dados['painel']);
            $cmd->bindValue(':senha',md5($dados['senha']));
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados inseridos!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function prepareUpdateFuncionario($dados, $id)
    {
        $validation = self::requiredParametros($dados);

        if(is_null($validation)){            
            return $this->updateFuncionario($dados, $id); 
        }

        return $validation;
    }

    private function updateFuncionario($dados, int $id)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "UPDATE 
                    $this->model 
                SET 
                    nome = :nome, 
                    email = :email, 
                    status = :status,
                    painel = :painel,
                    senha = :senha
                WHERE 
                    id = :id"
                );

            $cmd->bindValue(':nome',$dados['nome']);
            $cmd->bindValue(':email',$dados['email']);
            $cmd->bindValue(':status',$dados['status']);
            $cmd->bindValue(':painel',$dados['painel']);
            $cmd->bindValue(':senha',md5($dados['senha']));
            $cmd->bindValue(':id',$id);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados Atualizados!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function getAll()
    {
        $cmd = $this->conexao->query(
            "SELECT 
                id,nome, email, painel, status
            FROM
                $this->model
            WHERE status = 1 
            order by nome asc"
        );

        return $cmd->fetchAll();
    }

    public function findFuncionarios($request)
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                * 
            FROM
                $this->model
            WHERE 
                email
            LIKE
                '%$request%'
            OR
                nome
            LIKE
                '%$request%'"
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return false;
        
    }

    public function prepareChangedFuncionario($id)
    {
        $funcionario = self::findById($id);

        if(is_null($funcionario)) {
            return self::messageWithData(422, 'Funcionario não encontrado', []);
        }

        $funcionario['status'] == '1' ? $funcionario = 0 : $funcionario = 1;
        return $this->updateStatusFuncionario(
            $funcionario,
            $id);
    }

    private function updateStatusFuncionario($status, $id)
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
}