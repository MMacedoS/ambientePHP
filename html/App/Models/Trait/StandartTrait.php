<?php

trait StandartTrait {

    public function message($status, $message){
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    public function messageWithData($status, $message, $data){
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public function requiredParametros(array $dados)
    {
         foreach ($dados as $key => $value) {
             if(empty($value))
             {
                 return self::message(422, 'preencha os parametros');
             }
         }
    }

    public function prepareGetIdApartamento($array)
    {
        return array_map(function($item)
        {
            return $item['apartamento_id'];
        }, $array);
    }

    public function prepareStatusTable($table, $status,$where = null)
    {
        if(is_null($where)) {
            return false;
        }

        $cmd = $this->conexao->prepare(
            "UPDATE $table SET status = '$status' WHERE $where"
        );

        if($cmd->execute())
        {
            return true;
        }

        return false;
    }

}