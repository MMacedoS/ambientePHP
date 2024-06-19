<?php

trait RequestTrait{

    function decodeRequestData() {
        $method = $_SERVER['REQUEST_METHOD'];
      
        if ($method === 'GET') {
          return $_GET;
        } else if ($method === 'POST') {
          return $_POST;
        } else if ($method === 'PUT' || $method === 'DELETE') {
          $data = file_get_contents('php://input');
          parse_str($data, $params);
          return $params;
        } else {
          return [];
        }
    }

    function splitString($inputString) {
        $prefix1 = '';
        $prefix2 = '';
        $prefix3 = '';
        $prefix4 = '';

        if (is_null($inputString)) {
            return null;
        }
    
        $delim = "_@_";
    
        // Verifica se o delimitador está presente na string de entrada
        if (strpos($inputString, $delim) === false) {
            return $inputString;
        }
    
        $parts = explode($delim, $inputString);
    
        // Verifica se a string possui os quatro prefixos separados corretamente
        if (count($parts) !== 5) {
            // Atribui as partes separadas em variáveis individuais
            $prefix1 = $parts[0];
            $prefix2 = $parts[1];
            $prefix3 = $parts[2];
            $prefix4 = $parts[3];
        } 
        
        // Retorna um array associativo com as variáveis
        return [
            'prefix1' => $prefix1,
            'prefix2' => $prefix2,
            'prefix3' => $prefix3,
            'prefix4' => $prefix4
        ];
    
        
    }  
      
}