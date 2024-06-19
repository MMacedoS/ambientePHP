<?php

trait DateModelTrait {

    public function addDayInDate($date = null, $intervalo)
    {
        $date = new DateTime($date);
        date_add(
            $date,  
            date_interval_create_from_date_string("$intervalo days")
        );
        return $date->format('Y-m-d H:i:s');
        
    }

    public function countDaysInReserva($dados = null)
    {
        if(is_null($dados)){
            return 1;
        }
        
        $data_entrada = strtotime($dados->dataEntrada);
        $data_saida = strtotime($dados->dataSaida);

        $days = ($data_saida - $data_entrada) / 86400;

        return $days;
    }

}