<?php

require_once 'Trait/StandartTrait.php';
require_once 'Trait/FindTrait.php';
require_once 'Trait/DateModelTrait.php';

class ReservaModel extends ConexaoModel {

    use StandartTrait;
    use FindTrait;
    use DateModelTrait;
    
    protected $conexao;

    protected $model = 'reserva';

    protected $apartamento_model;

    protected $consumo_model;

    public function __construct() 
    {
        $this->conexao = ConexaoModel::conexao();

        $this->apartamento_model = new ApartamentoModel();
        $this->consumo_model = new ConsumoModel();
    }

    public function prepareInsertReserva($dados)
    {
        $validation = self::requiredParametros($dados);

        if(is_null($validation)){
            
            if($this->verificareservaSeExiste($dados))
            {   
                return $this->insertReserva($dados); 
            }

            return self::message(422, 'Reserva existente!');
        }

        return $validation;
    }

    private function verificaReservaSeExiste($dados)
    {
        $hospede = (int)$dados['hospedes'];
        $dataEntrada = (string)$dados['entrada'];
        $dataSaida = (string)$dados['saida'];
        $tipo = (int)$dados['tipo'];
        $apartamento = (int)$dados['apartamento'];
        $status = (int)$dados['status'];
        
        $cmd = $this->conexao->query(
            "SELECT 
                *
            FROM
                $this->model
            WHERE
                hospede_id = '$hospede'
            AND 
                apartamento_id = '$apartamento'
            AND
                tipo = '$tipo'
            AND
                dataEntrada = '$dataEntrada'
            AND
                dataSaida = '$dataSaida'"
        );

        if($cmd->rowCount()>0)
        {
            return false;
        }

        return true;
    }

    private function insertReserva($dados)
    {
        $this->conexao->beginTransaction();
        try {      
            $cmd = $this->conexao->prepare(
                "INSERT INTO 
                    $this->model 
                SET 
                    dataReserva = :dataReserva, 
                    dataEntrada = :dataEntrada, 
                    dataSaida = :dataSaida,
                    obs = :observacao,
                    apartamento_id = :apartamento_id,
                    tipo = :tipo,
                    hospede_id = :hospede_id,
                    valor = :valor,
                    status = :status,
                    funcionario =  :funcionario,
                    qtde_hosp = :qtde_hosp
                    "
                );

            $cmd->bindValue(':dataReserva', Date('Y-m-d'));
            $cmd->bindValue(':dataEntrada',$dados['entrada']);
            $cmd->bindValue(':dataSaida',$dados['saida']);
            $cmd->bindValue(':apartamento_id',$dados['apartamento']);
            $cmd->bindValue(':valor',$dados['valor']);
            $cmd->bindValue(':status',$dados['status']);
            $cmd->bindValue(':tipo',$dados['tipo']);
            $cmd->bindValue(':observacao',$dados['observacao']);
            $cmd->bindValue(':hospede_id',$dados['hospedes']);
            $cmd->bindValue(':funcionario',$_SESSION['code']);
            $cmd->bindValue(':qtde_hosp',$dados['qtde_hosp']);
            $dados = $cmd->execute();

            $this->conexao->commit();
            return self::message(200, "dados inseridos!!");

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

    public function prepareUpdatereserva($dados, $id)
    {
        $validation = self::requiredParametros($dados);

        if(is_null($validation)){            
            return $this->updateReserva($dados, $id); 
        }

        return $validation;
    }

    private function updateReserva($dados, int $id)
    {
        $this->conexao->beginTransaction();
        try {               
            $cmd = $this->conexao->prepare(
                "UPDATE 
                    $this->model 
                SET 
                    dataEntrada = :dataEntrada, 
                    dataSaida = :dataSaida,
                    obs = :observacao,
                    apartamento_id = :apartamento_id,
                    tipo = :tipo,
                    hospede_id = :hospede_id,
                    valor = :valor,
                    status = :status,
                    placa = :placa,
                    funcionario =  :funcionario,
                    qtde_hosp = :qtde_hosp
                WHERE 
                    id = :id"
                );

                $cmd->bindValue(':dataEntrada',$dados['entrada']);
                $cmd->bindValue(':dataSaida',$dados['saida']);
                $cmd->bindValue(':apartamento_id',$dados['apartamento']);
                $cmd->bindValue(':valor',$dados['valor']);
                $cmd->bindValue(':status',$dados['status']);
                $cmd->bindValue(':tipo',$dados['tipo']);
                $cmd->bindValue(':observacao',$dados['observacao']);
                $cmd->bindValue(':hospede_id',$dados['hospedes']);
                $cmd->bindValue(':placa',$dados['placa']);
                $cmd->bindValue(':funcionario',$_SESSION['code']);
                $cmd->bindValue(':qtde_hosp',$dados['qtde_hosp']);
                $cmd->bindValue(':id',$id);
                $dados = $cmd->execute();
            
            $diariaModel =  new DiariasModel();
            $diariaModel->calculeReserva($id);
            $this->conexao->commit();
            return self::message(200, "dados Atualizados!!");

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function findReservas($nome, $entrada, $saida, $status)
    {
        $SQL = "SELECT 
                   r.id,
                    h.nome,
                    r.dataEntrada,
                    r.dataSaida,
                    p.numero,
                    r.status
                FROM 
                    $this->model r 
                left join
                    hospede h on h.id = r.hospede_id
                left join 
                    apartamento p on  p.id = r.apartamento_id
                WHERE
                    r.status LIKE '%$status%' 
                ";
        
        if(!empty($entrada) && !empty($saida)){
            $SQL.= "
            AND
            (
                dataEntrada 
                    BETWEEN 
                        '$entrada' 
                    AND 
                        '$saida'
                                       
            )";
        }

        if(!empty($nome)){
            $SQL.= "
            AND
            (
                h.nome LIKE '%$nome%' 
                                       
            )";
        }

        $SQL.= " order by p.numero asc";

        $cmd  = $this->conexao->query(
            $SQL
        );

        if($cmd->rowCount() > 0)
        {            
            return $cmd->fetchAll();
        }

        return false;
        
    }

    public function getAll() {
        $cmd = $this->conexao->query(
            "SELECT 
                r.id,
                h.nome,
                r.dataEntrada,
                r.dataSaida,
                p.numero,
                r.status
            FROM
                $this->model r
            left join
            hospede h
            on h.id = r.hospede_id
            left join apartamento p on  p.id = r.apartamento_id
            where r.status != 3 and r.status != 4
            order by r.id desc"
        );
        
        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }
    }

    public function findAllReservas($nome, int $off = 0)
    {
        $off = $off;
        $cmd  = $this->conexao->query(
            "SELECT 
                r.*, 
                h.nome, 
                a.numero 
            FROM 
                $this->model r 
            INNER JOIN
                hospede h 
            ON 
                r.hospede_id = h.id
            LEFT JOIN 
                empresa_has_hospede eh
            ON 
                eh.hospede_id = h.id
            INNER JOIN 
                apartamento a 
            ON 
                r.apartamento_id = a.id
            WHERE
                h.nome LIKE '%$nome%'
             AND
                r.status <=2   
             AND
                r.dataEntrada >= DATE_SUB(curdate(), INTERVAL 3 DAY) 
            ORDER BY
                r.id DESC
            LIMIT 12 offset $off 
            
            "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
        
    }

    public function getAllReservas($hospede = '', $dataEntrada = '', $dataSaida = '', $situacao = 1 )
    {
        try {
          
            if ($this->conexao === null) {
                throw new Exception("Database connection is not established.");
            }
            
            $query = "
                SELECT
                    r.id,
                    r.dataEntrada,
                    r.dataSaida,
                    r.tipo,
                    r.qtde_hosp,
                    r.status,
                    h.nome,
                    a.numero,
                    r.valor AS custo,
                    SUM(COALESCE(d.valor, 0)) AS valor
                FROM
                    $this->model r
                INNER JOIN
                    hospede h ON r.hospede_id = h.id
                INNER JOIN
                    apartamento a ON r.apartamento_id = a.id
                LEFT JOIN
                    diarias d ON d.reserva_id = r.id AND d.status = 1
                WHERE
                    h.nome LIKE :hospede
                    AND (
                        (r.dataEntrada BETWEEN :dataEntrada AND :dataSaida)
                        OR
                        (r.dataSaida BETWEEN :dataEntrada AND :dataSaida)
                    )
                    AND r.status LIKE :situacao
                GROUP BY
                    r.id,
                    r.dataEntrada,
                    r.dataSaida,
                    r.tipo,
                    r.qtde_hosp,
                    r.status,
                    h.nome,
                    a.numero
                ORDER BY
                    r.id DESC
            ";
            
            $cmd = $this->conexao->prepare($query);
            $hospedeParam = "%$hospede%";
            $situacaoParam = "%$situacao%";
            $cmd->bindParam(':hospede', $hospedeParam, PDO::PARAM_STR);
            $cmd->bindParam(':dataEntrada', $dataEntrada, PDO::PARAM_STR);
            $cmd->bindParam(':dataSaida', $dataSaida, PDO::PARAM_STR);
            $cmd->bindParam(':situacao', $situacaoParam, PDO::PARAM_STR);
            $cmd->execute();
            
            if ($cmd->rowCount() > 0) {
                return $cmd->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return []; 
          

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        
    }

    public function prepareChangedReserva($id)
    {
        $reserva = self::findById($id);

        if(is_null($reserva)) {
            return self::messageWithData(422, 'reserva não encontrado', []);
        }

        $reserva['status'] == '1' ? $status = 5 : $status = 1;
        
        return $this->updateStatusReserva(
                $status,
                $id,
		        $reserva['apartamento_id']
            );
    }

    private function updateStatusReserva($status, $id, $apartamento, $valor_reserva = 100)
    {
        $this->model = 'reserva';
        try {      
            $cmd = $this->conexao->prepare(
                "UPDATE 
                    $this->model 
                SET 
                    status = :status,
                    gerarDiaria = :data
                WHERE 
                    id = :id"
                );
	        $cmd->bindValue(':status', $status);
	        $cmd->bindValue(':data', Date('Y-m-d 15:00:00'));
            $cmd->bindValue(':id', $id);
            $cmd->execute();
            
            $this->apartamento_model->prepareChangedApartamentoStatus($apartamento, 2);

            if ($status == 3) {
                $diaria_model = new DiariasModel();
                $diaria_model->inserirDiaria(
                    [
                        'valor' => $valor_reserva,
                        'data' => Date('Y-m-d')
                    ],
                    $id
                );
	        }

            return self::messageWithData(200, "dados Atualizados!!", []);

        } catch (\Throwable $th) {
            $this->logError($th->getMessage());
            return self::message(422, $th->getMessage());
        }
    }

    private function updateStatusCheckoutReserva($status, $id, $apartamento)
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
            
            $this->apartamento_model->prepareChangedApartamentoStatus($apartamento, 1);

            $this->conexao->commit();
            
            return self::messageWithData(200, "dados Atualizados!!", []);

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::message(422, $th->getMessage());
        }
    }

    public function prepareCheckinReserva($id, $placa)
    {
        try {
            $this->conexao->beginTransaction();
            $reserva = self::findById($id);

            if(is_null($reserva)) {
                $this->conexao->rollback();
                return self::messageWithData(422, 'reserva não encontrado', []);
            }
            
            $this->model = 'apartamento';
            $apartamento = $this->apartamento_model->findById($reserva['apartamento_id']);

            if($apartamento['status'] != 1) {
                $this->conexao->rollback();
                return self::messageWithData(422, 'Apartamento não esta disponivel', []);
            }

            $res = $this->updateStatusReserva(
                3,
                $id,
                $apartamento['id'],
                $reserva['valor']
            );

            if($res['status'] === 422) {
                $this->conexao->rollback();
                return self::messageWithData(422, 'reserva não esta atualizada', []);;
            }

            $this->updatePlacaByReservaId($id, $placa);

            $diariaModel =  new DiariasModel();
            $diariaModel->calculeReserva($id);
            $this->conexao->commit();

            return $res;
        } catch (\Throwable $th) {
            $this->conexao->rollback();
            $this->logError($th->getMessage());
            return self::messageWithData(422, 'reserva não foi atualizada', []);
            //throw $th;
        }
    }

    public function updatePlacaByReservaId($id, $placa)
    {
        $cmd = $this->conexao->query(
            "UPDATE reserva set placa = '$placa' WHERE id = '$id'"
        );
    }

    public function apartamentoDisponiveisPorData($dataStart, $dataEnd)
    {
        $dados = [];

        $this->conexao->beginTransaction();

        $arrayReservas = self::prepareGetIdApartamento(
            $this->getReservasPorData($dataStart, $dataEnd)
        );

        try {
            $cmd = $this->conexao->query(
                "SELECT 
                    id, 
                    numero 
                FROM 
                    apartamento 
                WHERE 
                    id not in 
                (
                    SELECT 
                        apartamento_id
                    FROM 
                        reserva
                    WHERE 
                        (status <= 3)
                    AND 
                    ( 
                        (
                            dataEntrada >= '$dataStart' 
                            AND 
                            dataEntrada < '$dataEnd'
                        ) 
                        OR 
                        (
                            dataSaida > '$dataStart' 
                            AND 
                            dataSaida <= '$dataEnd'
                        ) 
                        OR 
                        (
                            dataEntrada <= '$dataStart' 
                            AND 
                            dataSaida >= '$dataEnd' 
                        )
                    )
                )
                "
            );
    
            if($cmd->rowCount() > 0)
            {
                $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);
            }
            $this->conexao->commit();
            return self::messageWithData(200, 'apartamentos encontrados', $dados);

        } catch (\Throwable $th) {
            $this->conexao->rollback();
            return self::messageWithData(422, $th->getMessage(), []);
        }
    }

    private function getReservasPorData($dataStart, $dataEnd)
    {
        $cmd = $this->conexao->query(
            "SELECT 
                *
            FROM 
                reserva
            WHERE 
                (status <= 3)
            AND 
            ( 
                (
                    dataEntrada >= '$dataStart' 
                    AND 
                    dataEntrada < '$dataEnd'
                ) 
                OR 
                (
                    dataSaida > '$dataStart' 
                    AND 
                    dataSaida <= '$dataEnd'
                ) 
                OR 
                (
                    dataEntrada <= '$dataStart' 
                    AND 
                    dataSaida >= '$dataEnd' 
                )
            )"
        );

        if($cmd->rowCount() > 0) {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public function buscaReservasConcuidas($texto)
    {
        $texto = trim($texto[0]);

        $cmd  = $this->conexao->query(
            "SELECT 
                r.*, 
                h.nome, 
                a.numero 
            FROM 
                $this->model r 
            INNER JOIN
                hospede h 
            ON 
                r.hospede_id = h.id
            LEFT JOIN 
                empresa_has_hospede eh
            ON 
                eh.hospede_id = h.id
            INNER JOIN 
                apartamento a 
            ON 
                r.apartamento_id = a.id
            WHERE
                h.nome LIKE '%$texto%'         
            OR 
                r.id LIKE '%$texto%  
            "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];        
    }

    public function buscaHospedadas($texto)
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                r.*, 
                h.nome, 
                a.numero 
            FROM 
                $this->model r 
            INNER JOIN
                hospede h 
            ON 
                r.hospede_id = h.id
            LEFT JOIN 
                empresa_has_hospede eh
            ON 
                eh.hospede_id = h.id
            INNER JOIN 
                apartamento a 
            ON 
                r.apartamento_id = a.id
            WHERE
               r.status = 3
            AND
                h.nome LIKE '%$texto%' 
            OR 
                r.id = '$texto'
            
            order by a.numero asc
            "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
        
    }

    public function executaCheckout($id)
    {
        $reserva = self::findById($id);

        if(is_null($reserva)) {
            return self::messageWithData(422, 'reserva não encontrado', []);
        }

        $apartamento = $this->apartamento_model->findById($reserva['apartamento_id'])['id'];

        return $this->updateStatusCheckoutReserva(
            4,
            $id,
            $apartamento
        );
    }

    public function buscaCheckin($nome)
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                r.*, 
                h.nome, 
                a.numero 
            FROM 
                $this->model r 
            INNER JOIN
                hospede h 
            ON 
                r.hospede_id = h.id
            LEFT JOIN 
                empresa_has_hospede eh
            ON 
                eh.hospede_id = h.id
            INNER JOIN 
                apartamento a 
            ON 
                r.apartamento_id = a.id
            WHERE
               (
                    r.status = 1 
                OR
                    r.status= 2
               )
               AND
                dataEntrada <= curdate()
               AND
                dataEntrada >= DATE_SUB(curdate(), INTERVAL 1 DAY)
            AND
                h.nome LIKE '%$nome%' 
            order by a.numero asc
            "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return array();
        
    }

    public function buscaCheckout($nome)
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                r.*, 
                h.nome, 
                a.numero 
            FROM 
                $this->model r 
            INNER JOIN
                hospede h 
            ON 
                r.hospede_id = h.id
            LEFT JOIN 
                empresa_has_hospede eh
            ON 
                eh.hospede_id = h.id
            INNER JOIN 
                apartamento a 
            ON 
                r.apartamento_id = a.id
            WHERE
                r.status = 3
               AND
                dataSaida <= curdate()
            AND
                h.nome LIKE '%$nome%' 
            order by a.numero asc
            "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
        
    }

    public function buscaConfirmada($nome)
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                r.*, 
                h.nome, 
                a.numero 
            FROM 
                $this->model r 
            INNER JOIN
                hospede h 
            ON 
                r.hospede_id = h.id
            LEFT JOIN 
                empresa_has_hospede eh
            ON 
                eh.hospede_id = h.id
            INNER JOIN 
                apartamento a 
            ON 
                r.apartamento_id = a.id
            WHERE
                r.status = 2
            AND
                h.nome LIKE '%$nome%'
            AND 
                dataEntrada LIKE '%$nome%' 
            order by a.numero asc
            "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
        
    }

    public function buscaReservadas($nome)
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                r.*, 
                h.nome, 
                a.numero 
            FROM 
                $this->model r 
            INNER JOIN
                hospede h 
            ON 
                r.hospede_id = h.id
            LEFT JOIN 
                empresa_has_hospede eh
            ON 
                eh.hospede_id = h.id
            INNER JOIN 
                apartamento a 
            ON 
                r.apartamento_id = a.id
            WHERE
               (
                    r.status = 1 
                OR
                    r.status= 2
               )
               AND
                dataEntrada >= curdate()
               AND
                YEAR(dataEntrada) = YEAR(CURDATE())
            AND
                h.nome LIKE '%$nome%' 
            order by a.numero asc
            "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return array();
        
    }

    public function getDadosReservas($id){
        $cmd  = $this->conexao->query(
            "SELECT 
                r.*, 
                h.nome, 
                a.numero,
                COALESCE((SELECT sum(valorUnitario * quantidade) FROM consumo c where c.reserva_id = r.id and c.status = 1), 0) as consumos,
                COALESCE((SELECT sum(valor) FROM diarias d where d.reserva_id = r.id and d.status = 1), 0) as diarias,
                COALESCE((SELECT sum(p.valorPagamento) FROM pagamento p where p.reserva_id = r.id and p.status = 1), 0) as pag
            FROM 
                `reserva` r 
            INNER JOIN 
                hospede h 
            on 
                r.hospede_id = h.id 
            INNER JOIN 
                apartamento a 
            on 
                a.id = r.apartamento_id 
            WHERE 
                r.id = $id
            "
        );

        if($cmd->rowCount() > 0)
        {
            $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);
            return self::messageWithData(200, 'reserva encontrada', $dados);
        }

        return self::messageWithData(422, 'nehum dado encontrado', []);
    }

    public function getAllDadosReservasById($id)
    {
        $queryReserva = "
        SELECT 
            r.*, 
            h.nome, 
            a.numero,
            COALESCE(consumos.totalConsumo, 0) AS consumos,
            COALESCE(diarias.totalDiarias, 0) AS diarias,
            COALESCE(pag.totalPagamentos, 0) AS pag,
            ((COALESCE(consumos.totalConsumo, 0) + COALESCE(diarias.totalDiarias, 0)) - ( COALESCE(pag.totalPagamentos, 0))) AS subtotal
        FROM 
            `reserva` r 
        INNER JOIN 
            hospede h 
        ON 
            r.hospede_id = h.id 
        INNER JOIN 
            apartamento a 
        ON 
            a.id = r.apartamento_id 
        LEFT JOIN (
            SELECT reserva_id, SUM(valorUnitario * quantidade) AS totalConsumo
            FROM consumo
            WHERE status = 1
            GROUP BY reserva_id
        ) consumos
        ON 
            r.id = consumos.reserva_id
        LEFT JOIN (
            SELECT reserva_id, SUM(valor) AS totalDiarias
            FROM diarias
            WHERE status = 1
            GROUP BY reserva_id
        ) diarias
        ON 
            r.id = diarias.reserva_id
        LEFT JOIN (
            SELECT reserva_id, SUM(valorPagamento) AS totalPagamentos
            FROM pagamento
            WHERE status = 1
            GROUP BY reserva_id
        ) pag
        ON 
            r.id = pag.reserva_id
        WHERE 
            r.id = :id
    ";
    
    // Preparando e executando a consulta principal
    $cmd = $this->conexao->prepare($queryReserva);
    $cmd->bindParam(':id', $id, PDO::PARAM_INT);
    $cmd->execute();
    
    if ($cmd->rowCount() > 0) {
        $reserva = $cmd->fetch(PDO::FETCH_ASSOC);
    
        // Consulta para Detalhes dos Consumos
        $queryConsumos = "
            SELECT 
                descricao, 
                valorUnitario, 
                quantidade, 
                (valorUnitario * quantidade) AS total
            FROM 
                consumo 
            WHERE 
                reserva_id = :id 
                AND status = 1
        ";
    
        $cmdConsumos = $this->conexao->prepare($queryConsumos);
        $cmdConsumos->bindParam(':id', $id, PDO::PARAM_INT);
        $cmdConsumos->execute();
        $consumos = $cmdConsumos->fetchAll(PDO::FETCH_ASSOC);
        if (empty($consumos)) {
            $this->logError("Debug Consumos: Não há consumos para a reserva ID {$id}");
        }
    
        // Consulta para Detalhes das Diárias
        $queryDiarias = "
            SELECT 
                data, 
                valor                     
            FROM 
                diarias
            WHERE 
                reserva_id = :id 
                AND status = 1
        ";
        $cmdDiarias = $this->conexao->prepare($queryDiarias);
        $cmdDiarias->bindParam(':id', $id, PDO::PARAM_INT);
        $cmdDiarias->execute();
        $diarias = $cmdDiarias->fetchAll(PDO::FETCH_ASSOC);
        if (empty($diarias)) {
            $this->logError("Debug Diarias: Não há diárias para a reserva ID {$id}");
        }
    
        // Consulta para Detalhes dos Pagamentos
        $queryPagamentos = "
            SELECT 
                dataPagamento, 
                valorPagamento, 
                tipoPagamento
            FROM 
                pagamento 
            WHERE 
                reserva_id = :id 
                AND status = 1
        ";
        $cmdPagamentos = $this->conexao->prepare($queryPagamentos);
        $cmdPagamentos->bindParam(':id', $id, PDO::PARAM_INT);
        $cmdPagamentos->execute();
        $pagamentos = $cmdPagamentos->fetchAll(PDO::FETCH_ASSOC);
        if (empty($pagamentos)) {
            $this->logError("Debug Pagamentos: Não há pagamentos para a reserva ID {$id}");
        }
    
        // Incluindo os detalhes dos consumos, diárias e pagamentos na resposta
        $reserva['consumosDetalhados'] = $consumos;
        $reserva['diariasDetalhados'] = $diarias;
        $reserva['pagamentosDetalhados'] = $pagamentos;

        return self::messageWithData(200, 'Reserva encontrada', $reserva);
        } 

        return self::messageWithData(404, 'Reserva não encontrada', []);

    }

    private function insertDiariaConsumo($value, $data)
    {        
        $this->consumo_model->insertDiaria($value, $data);
    }

    // private function updateGerarDiaria($id, $data)
    // {
    //     $this->conexao->beginTransaction();
    //     try {      
    //         $cmd = $this->conexao->prepare(
    //             "UPDATE 
    //                 $this->model 
    //             SET 
    //                 gerarDiaria = :gerarDiaria
    //             WHERE 
    //                 id = :id"
    //             );
    //         $cmd->bindValue(':gerarDiaria',$data);
    //         $cmd->bindValue(':id',$id);
    //         $dados = $cmd->execute();

    //         $this->conexao->commit();
            
    //         return self::messageWithData(200, "dados Atualizados!!", []);

    //     } catch (\Throwable $th) {
    //         $this->conexao->rollback();
    //         return self::message(422, $th->getMessage());
    //     }
    // }

    public function findAllCafe()
    {
        $cmd  = $this->conexao->query(
            "SELECT 
                r.id, 
                h.nome, 
                a.numero,
                r.qtde_hosp 
            FROM 
                $this->model r 
            INNER JOIN
                hospede h 
            ON 
                r.hospede_id = h.id
            LEFT JOIN 
                empresa_has_hospede eh
            ON 
                eh.hospede_id = h.id
            INNER JOIN 
                apartamento a 
            ON 
                r.apartamento_id = a.id
            WHERE
                r.status = 3
            "
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
        
    }

    public function buscaMapaReservas($startDate,$endDate)
    {
        try {      
            $cmd = $this->conexao->prepare(
                "SELECT all_dates.date_value AS start, IFNULL(count(r.dataEntrada), 0) AS title
                FROM (
                    SELECT DATE_ADD(:entrada, INTERVAL n.num DAY) AS date_value
                    FROM (
                        SELECT (a.a + (10 * b.a) + (100 * c.a)) num
                        FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) c
                    ) n
                    WHERE DATE_ADD(:entrada, INTERVAL n.num DAY) <= :saida
                ) all_dates
                LEFT JOIN reserva r ON r.dataEntrada <= all_dates.date_value AND r.dataSaida >= all_dates.date_value
                where r.status < 4
                GROUP BY all_dates.date_value
                ORDER BY all_dates.date_value                
                "
                );

            $cmd->bindValue(':entrada', $startDate);
            $cmd->bindValue(':saida',$endDate);
            $cmd->execute();
            
            $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);

            
        // Converter os resultados para o formato do FullCalendar
        $eventos_fullcalendar = [];
        foreach ($dados as $evento) {
            if($evento['title'] > 0) {
                $evento_fullcalendar = [
                    'title' => $evento['title'] . " Apt reservados", // Título do evento, com a quantidade de reservas
                    'start' => $evento['start'], // Data de início da reserva
                ];
                $eventos_fullcalendar[] = $evento_fullcalendar;
            }
        }

        return $eventos_fullcalendar;

            
            // // Criar a tabela temporária
            //     $sql_create_tmp_table = "CREATE TEMPORARY TABLE tmp_dates (date_value DATE)";
            //     $this->conexao->exec($sql_create_tmp_table);

            //     // Preencher a tabela temporária com as datas do intervalo
            //     $sql_fill_tmp_table = "INSERT INTO tmp_dates (date_value)
            //                         SELECT DATE_ADD(:entrada, INTERVAL n.num DAY) AS date_value
            //                         FROM (
            //                             SELECT (a.a + (10 * b.a) + (100 * c.a)) num
            //                             FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
            //                             CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
            //                             CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) c
            //                         ) n
            //                         WHERE DATE_ADD(:entrada, INTERVAL n.num DAY) <= :saida";
            //     $this->conexao->prepare($sql_fill_tmp_table)->execute(['entrada' => $startDate, 'saida' => $endDate]);

            //     // Executar a consulta SQL com a tabela temporária
            //     $sql_query = "SELECT d.date_value AS start, COUNT(r.dataEntrada) AS title
            //                 FROM tmp_dates d
            //                 LEFT JOIN reserva r ON r.dataEntrada = d.date_value
            //                 WHERE d.date_value BETWEEN :entrada AND :saida
            //                 GROUP BY d.date_value
            //                 ORDER BY d.date_value";

            //     $stmt = $this->conexao->prepare($sql_query);
            //     $stmt->execute(['entrada' => $startDate, 'saida' => $endDate]);
            //     $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //     $eventos_fullcalendar = [];
            //     foreach ($eventos as $evento) {
            //         $evento_fullcalendar = [
            //             'title' => $evento['title'] . ' Apt Reservados', // Título do evento, concatenando a contagem com a string
            //             'start' => $evento['start'], // Data de início da reserva
            //             'end' => $evento['start'], // Neste exemplo, estamos assumindo que não há hora de término, então a data de início também é usada como data de término
            //         ];
            //         $eventos_fullcalendar[] = $evento_fullcalendar;
            //     }
                
            // return $eventos_fullcalendar;

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
