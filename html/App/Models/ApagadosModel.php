<?php

require_once 'Trait/FindTrait.php';

class ApagadosModel extends ConexaoModel {

    protected $conexao;
    protected $model;

    use FindTrait;

    public function __construct() 
    {
        $this->conexao = ConexaoModel::conexao();
        $this->model = "apagado";
    }

    public function getApagados() 
    {
        $cmd = $this->conexao->query(
            "SELECT 
                a.*,
                f.nome as nome_funcionario
            FROM
                apagado a left join usuarios f on a.funcionario = f.id 
            WHERE a.visualizado = 1
            ORDER BY a.id DESC"
        );
        return $cmd->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getApagadosByParam( object $param) 
    {
        $cmd = $this->conexao->query(
            "SELECT 
                a.*,
                f.nome as nome_funcionario
            FROM
                apagado a left join usuarios f on a.funcionario = f.id 
            WHERE motivo like '%$param->motivo%' and funcionario like '%$param->funcionario%
            ORDER BY a.id DESC"
        );
        return $cmd->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertApagados($dados, $motivo = null, $table = null, $id_table = null)
    {
        try {      
            $cmd = $this->conexao->prepare(
                "INSERT INTO 
                    apagado 
                SET 
                    funcionario = :funcionario, 
                    dados = :dados,
                    motivo = :motivo,
                    table_reference = :table_reference,
                    id_table = :id_table
                    "
                );

            $cmd->bindValue(':dados', json_encode($dados));
            $cmd->bindValue(':funcionario',$_SESSION['code']);
            $cmd->bindValue(':motivo', $motivo);
            $cmd->bindValue(':table_reference', $table);
            $cmd->bindValue(':id_table', $id_table);
            $dados = $cmd->execute();

           
            return true;

        } catch (\Throwable $th) {
            self::logError($th->getMessage(). $th->getLine());
            return $th->getMessage();
        }
    }

    public function updateApagadosStatus($id)
    {
        $apago =  $this->findById($id);
        $status = $apago['visualizado'] == 0 ? 1 : 0;
        try {      
            $cmd = $this->conexao->prepare(
                "UPDATE
                    apagado 
                SET 
                    visualizado = :visualizado
                WHERE id = :id
                    "
                );

            $cmd->bindValue(':visualizado', $status);
            $cmd->bindValue(':id', $id);
            $dados = $cmd->execute();           
            return true;

        } catch (\Throwable $th) {
            self::logError($th->getMessage(). $th->getLine());
            return $th->getMessage();
        }
    }

    public function changeAllStatusApagados()
    {
        try {      
            $cmd = $this->conexao->prepare(
                "UPDATE
                    apagado 
                SET 
                    visualizado = 0
                WHERE visualizado = 1
                    "
                );
            $dados = $cmd->execute();           
            return true;

        } catch (\Throwable $th) {
            self::logError($th->getMessage(). $th->getLine());
            return $th->getMessage();
        }
    }

    public function findByIdWithFuncionario($id) 
    {
        $cmd = $this->conexao->prepare(
            "SELECT 
                a.*,
                f.nome as nome_funcionario
            FROM
                $this->model a 
            LEFT JOIN usuarios f ON a.funcionario = f.id
            WHERE
                a.id = $id
            "
        );

        $cmd->execute();

        if($cmd->rowCount() > 0)
        {

            return $cmd->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    
}