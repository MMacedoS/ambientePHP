<?php

class ReservaController extends \Controller{
    use DateTrait;

    protected $reserva_model;

    public function __construct() 
    {
        $this->validPainel(); 
        $this->reserva_model = new ReservaModel(); 
        $this->atualizaDiaria();     
    }

    public function atualizaDiariaNotExists($reservaId)
    {
        $reservaService = new ReservaService();
        $reservaService->criarDiarias($reservaId);
    } 

    public function atualizaDiaria()
    {
        $diarias_model = new DiariasModel();
        $diarias_model->obterReservasHospedadas();
    }

    public function getAll() {
        $reservas = $this->reserva_model->getAll();
        echo json_encode($reservas);
    }

    public function getAllNota() {
        $reservas = $this->reserva_model->findReservas('','','',3);
        echo json_encode($reservas);
    }

    public function buscaReservas($request =  null)
    {    
        echo json_encode($this->reserva_model->findReservas(
            $_POST['busca'],
            $_POST['dt_entrada'], 
            $_POST['dt_saida'], 
            $_POST['status']
        ));
    }

    public function buscaReservaPorId($id)
    {
        echo json_encode(self::messageWithData(201,'Dados encontrados', $this->reserva_model->findById($id)));
    }

    public function salvarReservas() {
        $reserva = $this->reserva_model->prepareInsertReserva($_POST);
        echo json_encode($reserva);
    }

    public function atualizarReserva($id)
    {
        $reserva = $this->reserva_model->prepareUpdateReserva($_POST, $id);
        echo json_encode($reserva);
    }

    public function changeStatusReservas($id)
    {
        $reserva = $this->reserva_model->prepareChangedReserva($id);
        echo json_encode($reserva);
    }

    public function reservaBuscaPorData(){
        $reserva = $this->reserva_model->apartamentoDisponiveisPorData($_POST['dataEntrada'], $_POST['dataSaida']);
        echo json_encode($reserva);
    }

    public function buscaHospedadas($request = null)
    {
        $consulta = strpos('@fechado', $request);

        if($consulta) return $this->reserva_model->buscaReservasConcuidas(explode('@',$consulta));

        $reserva = $this->reserva_model->buscaHospedadas($request);
        return $reserva;
    }

    public function executaCheckout($id) 
    {
        $reserva = $this->reserva_model->executaCheckout($id);
        echo json_encode($reserva);
    }

    public function buscaCheckin($request = null)
    {
        $reserva = $this->reserva_model->buscaCheckin($request);
        return $reserva;
    }

    public function changeCheckinReservas($code)
    {
        $placa = empty($_POST) ? null : $_POST['placa'];
        $reserva = $this->reserva_model->prepareCheckinReserva($code, $placa);
        echo json_encode($reserva);
    }

    public function buscaCheckout($request = null)
    {
        $reserva = $this->reserva_model->buscaCheckout($request);
        return $reserva;
    }

    public function getDadosReservas($code)
    {
        
        // $this->atualizaDiariaNotExists($code);
        $reserva = $this->reserva_model->getDadosReservas($code);
        echo json_encode($reserva);
    }

    public function buscaConfirmada($request = null)
    {
        $reserva = $this->reserva_model->buscaConfirmada($request);
        return $reserva;
    }

    public function findAllCafe() {
        echo json_encode($this->reserva_model->findAllCafe());
    }

    public function findAllReservas() {
        echo json_encode($this->reserva_model->getAllReservas());
    }

    public function findReservasByParams() {
        echo json_encode($this->reserva_model->getAllReservas(
                $_POST['hospede'], 
                $_POST['startDate'], 
                $_POST['endDate'], 
                $_POST['status']
            )
        );
    }

    public function buscaAllReservaPorId($id)
    {
        $reserva = $this->reserva_model->getAllDadosReservasById($id);
        echo json_encode($reserva);
    }
}