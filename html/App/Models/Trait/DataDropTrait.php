<?php


trait DataDropTrait{
    public function dropRegister($dados)
    {  
        $cmd = $this->conexao->prepare(
            "INSERT INTO 
                apagado 
            SET 
               funcionario = :funcionario, 
               dados = :dados
            "
         );

        $cmd->bindValue(':dados', json_encode($dados));
        $cmd->bindValue(':funcionario',$_SESSION['code']);
        $dados = $cmd->execute();       
        return true;
    }
}