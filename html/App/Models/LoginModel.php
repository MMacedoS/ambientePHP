<?php

class LoginModel extends ConexaoModel
{
    public function logar($params)
    {
        $con = ConexaoModel::conexao();
        $cmd = $con->prepare('SELECT * from usuarios where email = :usuario and senha = :senha and status=:status');
        $cmd->bindValue(":usuario", $params['user']);
        $cmd->bindValue(":senha", md5($params['password']));
        $cmd->bindValue(":status",'1');
        $cmd->execute();

        if($cmd->rowCount()>0)
        {
            return $cmd->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }
}