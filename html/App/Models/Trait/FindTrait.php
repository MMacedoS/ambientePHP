<?php


trait FindTrait{
    public function findById($id)
    {
        $cmd = $this->conexao->prepare(
            "SELECT 
                *
            FROM
                $this->model
            WHERE
                id = $id
            "
        );

        $cmd->execute();

        if($cmd->rowCount() > 0)
        {

            return $cmd->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    public function findAll($params) 
    {
        $cmd = $this->conexao->prepare(
            "SELECT 
                *
            FROM
                $this->model
            WHERE
                $params
            "
        );

        $cmd->execute();

        if($cmd->rowCount() > 0)
        {

            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }
}