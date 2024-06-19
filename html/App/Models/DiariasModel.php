<?php

require_once 'Trait/StandartTrait.php';
require_once 'Trait/FindTrait.php';
require_once 'Trait/DateModelTrait.php';

class DiariasModel extends ConexaoModel {

    use StandartTrait;
    use FindTrait;
    use DateTrait;
    
    protected $conexao;

    protected $model = 'diarias';

    public function __construct() 
    {
        $this->conexao = ConexaoModel::conexao();
    }

    public function obterReservasHospedadas() 
    {
        try{
            $query = "SELECT id, gerarDiaria, valor FROM reserva WHERE status = 3 AND tipo = 1";

            // Prepara a consulta
            $stmt = $this->conexao->prepare($query);

            // Executa a consulta
            $stmt->execute();

            // Retorna os resultados como um array associativo
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->atualizarGerarDiaria($reservas);
            
        }
        catch(PDOException $e) {
            return null;
        }
    }

    function atualizarGerarDiaria($reservas) 
    {
        try {
            foreach ($reservas as $reserva) {
                $id = $reserva['id'];
                $gerarDiaria = $reserva['gerarDiaria'];
                $valor = $reserva['valor'];
    
                // Verifica se gerarDiaria está 24 horas antes da data e hora atual
                $dataAtual = new DateTime();
                $dataAtual->sub(new DateInterval('P1D')); // Subtrai 1 dia da data atual
    
                $dataGerarDiaria = new DateTime($gerarDiaria);

                $diferencaDias = $dataGerarDiaria->diff(new DateTime())->days;
    
		if ($diferencaDias > 0) {
                    for ($i = 0; $i < $diferencaDias; $i++) {
                        $data = $dataGerarDiaria->add(new DateInterval('P1D'));

                        $this->inserirDiaria(
                            [
                            'valor' => $valor,
                            'data' => $data->format('Y-m-d')
                            ],
                            $id
                        );
                    //Atualiza o gerarDiaria para a data e hora atual
                    $query = "UPDATE reserva SET gerarDiaria = :data WHERE id = :id";
    
                    // Prepara a atualização
                    $stmt = $this->conexao->prepare($query);
    
                    // Atribui valores aos parâmetros da consulta
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':data', $data->format('Y-m-d H:i:s'));
    
                    // Executa a atualização
                    $stmt->execute();
                    
                    }
                }
            }
        } catch (\Throwable $th) {
            self::logError($th->getMessage() . $th->getLine());
        }
    }

    public function inserirDiaria($dados, $reserva_id)
    {

        $query = "SELECT COUNT(*) as registros FROM diarias WHERE data = :data and id = :id";

        // Prepara a consulta
        $stmt = $this->conexao->prepare($query);

        // Atribui valores aos parâmetros da consulta
        $stmt->bindValue(':data', $dados['data']);
        $stmt->bindValue(':id', $reserva_id);

        // Executa a consulta
        $stmt->execute();

        // Obtém o resultado
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['registros'] < 1) {
            $this->conexao->beginTransaction();
            try {      
                $cmd = $this->conexao->prepare(
                    "INSERT INTO 
                        $this->model 
                    SET 
                        reserva_id = :reserva_id, 
                        data = :data, 
                        valor = :valor
                        "
                    );

                $cmd->bindValue(':reserva_id',$reserva_id);
                $cmd->bindValue(':data',$dados['data']);
                $cmd->bindValue(':valor',$dados['valor']);
                $dados = $cmd->execute();

                $this->conexao->commit();
                return self::message(200, "dados inseridos!!");

            } catch (\Throwable $th) {
                $this->conexao->rollback();
                
            }
        }
    }
    
    public function getDadosDiarias($id){
        $cmd  = $this->conexao->query(
            "SELECT 
                *
            FROM 
                diarias
            WHERE 
                reserva_id = $id
                and status = 1
            order by data DESC
            "
        );

        if($cmd->rowCount() > 0)
        {
            $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);
            return self::messageWithData(200, 'diarias encontrados', $dados);
        }

        return self::messageWithData(200, 'nenhum dado encontrado', []);
    }

    public function findDiariasById($id)
    {
        $cmd = $this->conexao->query(
            "SELECT 
                *
            FROM
                diarias
            WHERE
                id = $id
            "
        );

        if($cmd->rowCount() > 0)
        {

            return self::messageWithData(200,'Dados encontrados', $cmd->fetchAll(PDO::FETCH_ASSOC));
        }

        return false;
    }

    public function updateDiarias($dados, $id)
    {
        $valor = $dados['valor'];
        $data =  $dados['data'];
        
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "UPDATE 
                    diarias
                SET 
                    data = :data, 
                    valor = :valor
                WHERE 
                    id = :id
                    "
                );

            $cmd->bindValue(':data',$data);
            $cmd->bindValue(':valor',$valor);
            $cmd->bindValue(':id',$id);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados atualizados!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    private function removerValoresDiaria($idReserva) {
        $cmd = $this->conexao->prepare(
            "DELETE FROM
                diarias
            WHERE   
                reserva_id = :reserva_id"
            );

        $cmd->bindValue(':reserva_id', $idReserva);
        $cmd->execute();
    }

    public function calculeReserva(int $id)
    {
        $this->model = 'reserva';
        $reserva = self::findById($id);
            
        if($reserva['status'] == 3) {
            $this->inserirValoresDiaria(
                $reserva['dataEntrada'], 
                $this->addDayInDate(Date('Y-m-d'), 1), 
                $reserva['valor'] ,
                 $id,
                $reserva['tipo']
             );
         }
    }

    public function inserirValoresDiaria($dataInicial, $dataFinal, $valor, $idReserva, $tipo = 1) {
        $dataAtual = new DateTime($dataInicial);
        $dataFim = new DateTime($dataFinal);

        while ($dataAtual < $dataFim) {
            $valorData = $dataAtual->format('Y-m-d');
            $this->inserirDiaria(
                [
                'data'  => $valorData,
                'valor' => $valor
                ],
                $idReserva
            );
            $dataAtual->modify('+1 day');
            if($tipo != 1) {
                break;
            }
        }
    }

    public function getRemoveDiarias($id, $motivo)
    {
        $this->conexao->beginTransaction();
        try {      
            $dados = $this->findById($id);
            $dados = $dados ?? null;

            if (is_null($dados)) {              
                $this->conexao->rollback();
                return null;
            }

            $this->prepareStatusTable('diarias', 0," id = $id");
            
            $apagadosModel = new ApagadosModel();        
            if(!$apagadosModel->insertApagados($dados, $motivo, 'diarias', $id)) {
                $this->conexao->rollback();
                return null;
            };

            $this->conexao->commit();
            return true;
        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }
    
    // public function gerarDiarias()
    // {
    //     $cmd  = $this->conexao->query(
    //         "SELECT 
    //             *
    //         FROM 
    //             configuracao
    //         WHERE 
    //             parametro = 'gerar_diaria'
    //         "
    //     );

    //     if($cmd->rowCount() > 0)
    //     {
    //         $dados = $cmd->fetchAll(PDO::FETCH_ASSOC)[0]['valor'];
    //         // var_dump(strtotime(Date('Y-m-d 17:00:00')), strtotime(Date('Y-m-d H:i:s')));
    //         if (strtotime($dados) < strtotime(Date('Y-m-d H:i:s'))) {
    //             $this->verificaGerarDiarias($dados);
    //         }

    //         return true;
    //     }
        
    // }

    // private function verificaGerarDiarias($param)
    // {
    //     $cmd  = $this->conexao->query(
    //         "SELECT 
    //             id,
    //             valor,
    //             gerarDiaria
    //         FROM 
    //             reserva
    //         WHERE 
    //             status = 3
    //         AND
    //             tipo = 1
    //         AND
    //             gerarDiaria <= '$param'
    //         "
    //     );
       
    //     if($cmd->rowCount() > 0)
    //     {           
    //         $data = $cmd->fetchAll(PDO::FETCH_ASSOC);
    //         return $this->prepareGerarDiarias($data, $param);
    //     }

    //     return $this->updateConfiguracaoGerarDiaria(self::addDayInDate(Date('Y-m-d 16:00:00'), 1));
    // }

    // private function prepareGerarDiarias($dados, $param)
    // {
    //     if(empty($dados))
    //     {
    //         return null;
    //     }

    //     foreach ($dados as $key => $value) {
    //         $dias = round((strtotime(Date('Y-m-d H:i:s')) - strtotime($value['gerarDiaria']))/86400);

    //         if($dias < 1){
    //             $dias = 1;
    //         }

    //         for ($i=1; $i <= $dias; $i++) { 
    //             $this->insertDiariaConsumo($value, self::addDayInDate($param, $i -1 ));
    //             $this->updateGerarDiaria($value['id'], self::addDayInDate($param, $i));

    //             $this->updateConfiguracaoGerarDiaria(self::addDayInDate($param, $i));
    //         }            
    //     }

    //     return "atalizações consumos feitas";
    // }

    private function updateConfiguracaoGerarDiaria($data)
    {
        $cmd  = $this->conexao->prepare(
            "UPDATE 
                configuracao
            SET 
              valor = :data 
            WHERE 
                parametro = :param
            "
        );

        $cmd->bindValue(':data',$data);
        $cmd->bindValue(':param',"gerar_diaria");

        $cmd->execute();

        return "atualizado configurações";
    }
}
