<?php

class ReservaService
{
    use StandartTrait;
    use FindTrait;
    
    protected $conexao;

    public function __construct() 
    {
        $conn = new ConexaoModel();
        $this->conexao = $conn->conexao();
    }

    public function criarDiarias($reservaId)
    {
        try {
            $reserva = $this->buscarReserva($reservaId);
            $periodo = $this->calcularPeriodo($reserva['dataEntrada'], $reserva['dataSaida']);

            foreach ($periodo as $data) {
                $this->inserirDiariaSeNaoExistir($reservaId, $data->format('Y-m-d'), $reserva['valor']);
            }

            return "Diárias criadas com sucesso.";
        } catch (Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    }

    private function buscarReserva($reservaId)
    {
        $query = "
            SELECT dataEntrada, dataSaida, valor 
            FROM reserva 
            WHERE id = :reservaId
        ";

        $cmd = $this->conexao->prepare($query);
        $cmd->bindParam(':reservaId', $reservaId, PDO::PARAM_INT);
        $cmd->execute();

        if ($cmd->rowCount() === 0) {
            throw new Exception("Reserva não encontrada.");
        }

        return $cmd->fetch(PDO::FETCH_ASSOC);
    }

    private function calcularPeriodo($dataEntrada, $dataSaida)
    {
        $inicio = new DateTime($dataEntrada);
        $fim = new DateTime($dataSaida);

        return new DatePeriod($inicio, new DateInterval('P1D'), $fim);
    }

    private function diariaExiste($reservaId, $data)
    {
        $query = "
            SELECT COUNT(*) 
            FROM diarias 
            WHERE reserva_id = :reservaId 
            AND data = :data AND status = 1
        ";

        $cmd = $this->conexao->prepare($query);
        $cmd->bindParam(':reservaId', $reservaId, PDO::PARAM_INT);
        $cmd->bindParam(':data', $data, PDO::PARAM_STR);
        $cmd->execute();

        return $cmd->fetchColumn() > 0;
    }

    private function inserirDiaria($reservaId, $data, $valor, $status = 1)
    {
        $query = "
            INSERT INTO diarias (reserva_id, data, valor, status) 
            VALUES (:reservaId, :data, :valor, :status)
        ";

        $cmd = $this->conexao->prepare($query);
        $cmd->bindParam(':reservaId', $reservaId, PDO::PARAM_INT);
        $cmd->bindParam(':data', $data, PDO::PARAM_STR);
        $cmd->bindParam(':valor', $valor, PDO::PARAM_STR);
        $cmd->bindParam(':status', $status, PDO::PARAM_INT);
        $cmd->execute();
    }

    private function inserirDiariaSeNaoExistir($reservaId, $data, $valor)
    {
        if (!$this->diariaExiste($reservaId, $data)) {
            $this->inserirDiaria($reservaId, $data, $valor);
        }
    }
}

// $reservaService = new ReservaService($conexao);
// echo $reservaService->criarDiarias($reservaId);

