<?php

trait GeneralTrait{

    public function prepareStatusReserva($status){
        switch ($status) {
            case '1':
                    return 'Reservada';
                break;
            case '2':
                return 'Confirmada';
            break;

            case '3':
                return 'Hospedada';
            break;

            case '4':
                return 'Fechada';
            break;

            case '5':
                return 'Cancelada';
            break;
            
            default:
                # code...
                break;
        }
    }

    public function prepareTipoReserva($tipo) {
        switch ($tipo) {
            case '1':
                    return 'Diaria';
                break;
            case '2':
                return 'Pacote';
            break;

            case '3':
                return 'Promoção';
            break;
            
            default:
                # code...
                break;
        }
    }

   public function prepareTipo($value)
    {
        $res = "";
        switch ($value) {
            case '1':
                $res = "Dinheiro";
            break;
            case '2':
                $res = "Cartão de Crédito";
            break;
            case '3':
               $res =  "Cartão de Débito";
            break;
            case '4':
               $res =  "Déposito/PIX";
            break;
        }

        return $res;
    }

    public function prepareTipoSaidas($value)
    {
        $res = "";
        switch ($value) {
            case '1':
                $res = "Adiantamento";
            break;
            case '2':
                $res = "Operacionais";
            break;
            case '3':
               $res =  "Pessoal";
            break;
            case '4':
               $res =  "Outros";
            break;
        }

        return $res;
    }

    public function prepareTipoEntradas($value)
    {
        if(is_null($value))
        {
            return "Saida";

        }
        return "Entrada";
    }

    public function calculateEntrada($movimentos)
    {
        if(is_null($movimentos))
        {
            return 0;
        }

        $total = 0;

        foreach ($movimentos as $key => $value) {
            if(!is_null($value['entrada_id']))
            {
                $total+=$value['valor'];
            }
        }   

        return $total;
    }

    public function calculateSaida($movimentos)
    {
        if(is_null($movimentos))
        {
            return 0;
        }

        $total = 0;

        foreach ($movimentos as $key => $value) {
            if(!is_null($value['saida_id']))
            {
                $total+=$value['valor'];
            }
        }   

        return $total;
    }

    public function prepareTipoVenda($value)
    {
        if(is_null($value))
        {
            return "Vendas";

        }
        return "Hospedagem";
    }

    public function valueBr($value)
    {
        return number_format($value,2,',','.');
    }

    public function uploadFileWithHash($fileInputName, $uploadDirectory)
    {
        if (isset($_FILES[$fileInputName])) {
            $file = $_FILES[$fileInputName];

            // Verifica se houve algum erro no upload
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Gere um nome de arquivo único com hash
                $originalFileName = $file['name'];
                $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                $hashedFileName = md5(uniqid(rand(), true)) . '.' . $fileExtension;

                // Caminho completo para o arquivo de destino
                $destinationPath = $uploadDirectory . '/' . $hashedFileName;

                // Realiza o upload do arquivo
                if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
                    // Arquivo foi enviado com sucesso, retorne um array com os nomes
                    return [
                        'nome_original' => $originalFileName,
                        'imagem' => $hashedFileName,
                        'status' => 1
                    ];
                } 
                    return null;
            } 
                return null;
        } 
         return null;
    }

    public function deleteFile($uploadDirectory, $hashedFileName) {
        // Caminho completo para o arquivo a ser excluído
        $fileToDelete = $uploadDirectory . '/' . $hashedFileName;
        
        // Verifica se o arquivo existe e, em
        if (file_exists($fileToDelete)) {
            
            unlink($fileToDelete);
        }
    }

    public function formatPhoneNumber($number) {
        $number = preg_replace('/[^0-9]/', '', $number);
        $length = strlen($number);
        if ($length == 11) {
            $number = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $number);
        } elseif ($length == 10) {
            $number = preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $number);
        } else {
            return false;
        }
        return $number;
    }
    
}