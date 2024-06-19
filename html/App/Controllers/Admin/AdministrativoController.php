<?php

class AdministrativoController extends \Controller{
    protected $financeiro_controller;
    protected $apartamento_model;
    protected $produto_model;
    protected $reserva_model;
    protected $consumo_model;
    protected $pagamento_model;
    protected $app_model;
    protected $apagados_model;
    
    public function __construct() {
        $this->validPainel();     
        $this->financeiro_controller = new FinanceiroController();  
        $this->reserva_model = new ReservaModel();   
        $this->consumo_model = new ConsumoModel();    
        $this->pagamento_model = new PagamentoModel();   
        $this->app_model = new AppModel(); 
        $this->apagados_model = new ApagadosModel(); 
        $this->getApagados();      
    }

    public function getApagados() 
    {
        $this->apagados = $this->apagados_model->getApagados();
    }

    public function apagados($request = null)  
    {
        $this->active = "";    
        $this->viewAdmin('apagados');
        return;   
    }

    public function index() {           
        $this->active = "";    
        $this->viewAdmin('consultas');           
    }

    public function venda() {    
        $this->active = "venda";    
        $this->viewAdmin('venda');
    }

    public function apartamentos($request = null) {
        $this->active = "cadastro";    
        $this->viewAdmin('apartamentos',$request,"");
    }
    
    public function buscaApartamentos($request =  null)
    {
        $apartamento_controller = new ApartamentoController();
        return $apartamento_controller->buscaApartamentos($request);
    }

    public function funcionarios($request = null) {
        $this->active = "cadastro";    
        $this->viewAdmin('funcionarios',$request,"");
    }

    public function buscaFuncionarios($request =  null)
    {
        $funcionario_controller = new FuncionarioController();
        return $funcionario_controller->buscaFuncionarios($request);
    }

    public function hospedes($request = null) {
        $this->active = "cadastro";    
        $this->viewAdmin('hospedes',$request,"");
    }

    public function buscaHospedes($request =  null)
    {
        $hospedes_controller = new HospedeController();
        return $hospedes_controller->buscaHospedes($request);
    }

    public function reservas($request = null) {
        $this->active = "reservas";     
        $this->viewAdmin('reservas',$request,"");
    }

    public function consultas($request = null) {
        $this->active = "reservas";     
        $this->viewAdmin('consultas',$request,"");
    }

    public function mapas($request = null) {
        $this->active = "reservas";     
        $this->viewAdmin('mapas',$request,"");
    }

    public function notaClienteReserva($request = null)  
    {
        $this->active = "reservas";  
        $this->viewAdmin('nota-cliente-reservas',$request,"");
    }

    // public function buscaReservas($request =  null)
    // {        
    //     $reservas_controller = new ReservaController();
    //     return $reservas_controller->buscaReservas($request);
    // }

    public function hospedadas($request = null) {
        $this->active = "reservas";     
        $this->viewAdmin('hospedadas',$request,"");
    }

    public function buscaHospedadas($request =  null)
    {        
        $reservas_controller = new ReservaController();
        return $reservas_controller->buscaHospedadas($request);
    }

    public function checkin($request = null) {
        $this->active = "reservas";     
        $this->viewAdmin('checkin',$request,"");
    }

    public function financeiro($request = null) {
        $this->active = "reservas";     
        $this->viewAdmin('checkin',$request,"");
    }

    public function buscaCheckin($request =  null)
    {        
        $reservas_controller = new ReservaController();
        return $reservas_controller->buscaCheckin($request);
    }

    public function listproduto($request = null)
    {
        $this->produto_model = new ProdutoModel();     
        return $this->produto_model->getProdutos($request);
    }

    public function listApartamento()
    {
        $this->apartamento_model = new ApartamentoModel();     
        return $this->apartamento_model->getApartamento();
    }

    public function checkout($request = null) {
        $this->active = "reservas";     
        $this->viewAdmin('checkout',$request,"");
    }

    public function disponiveis($request = null){
        $this->active = "/";     
        $this->viewAdmin('disponiveis',$request,"");
    }

    public function buscaCheckout($request =  null)
    {        
        $reservas_controller = new ReservaController();
        return $reservas_controller->buscaCheckout($request);
    }

    public function confirmada($request = null) {
        $this->active = "reservas";     
        $this->viewAdmin('confirmada',$request,"");
    }

    public function buscaConfirmada($request =  null)
    {        
        $reservas_controller = new ReservaController();
        return $reservas_controller->buscaConfirmada($request);
    }

    public function produtos($request = null) {
        $this->active = "cadastro";     
        $this->viewAdmin('produtos',$request,"");
    }

    public function buscaProdutos($request =  null)
    {        
        $produto_controller = new ProdutoController();
        return $produto_controller->buscaProduto($request);
    }

    public function buscaEntradaProdutos($request =  null)
    {        
        $produto_controller = new ProdutoController();
        return $produto_controller->buscaEntradaProduto();
    }

    public function entradaEstoque($request = null) {
        $this->active = "estoque";     
        $this->viewAdmin('entradaEstoque',$request,"");
    }

    public function estoque($request = null) {
        $this->active = "estoque";     
        $this->viewAdmin('estoque',$request,"");
    }

    public function buscaEstoques($request = null) {
        $produto_controller = new ProdutoController();
        return $produto_controller->buscaEstoques($request);
    }

    public function buscaEntradaEstoques($request = null) {
        $request = self::splitString($request);
        $produto_controller = new ProdutoController();
        $dados = $produto_controller->buscaEntradaProduto($request);
  
        if(!empty($dados)){
            return $dados['data'];
        }

        return null;
    }

    public function buscaProdutosSelect()
    {        
        $produto_controller = new ProdutoController();
        return $produto_controller->buscaProdutos();
    }

    public function movimentacoes($request = null)
    {
        $this->active = "financeiro";     
        $this->viewAdmin('movimentacoes',$request,"");
    }

    public function buscaMovimentos($request = null)
    {
        return $this->financeiro_controller->buscaMovimentos($request);
    }

    public function entrada($request = null)
    {
        $this->active = "financeiro";     
        $this->viewAdmin('entrada',$request,"");
    }

    public function buscaEntrada($request = null)
    {
        return $this->financeiro_controller->buscaEntrada($request);
    }

    public function saida($request = null)
    {
        $this->active = "financeiro";     
        $this->viewAdmin('saida',$request,"");
    }

    public function buscaSaida($request = null)
    {
        return $this->financeiro_controller->buscaSaida($request);
    }

    public function cliente($request = null)
    {       
        $dados = (object)$this->reserva_model->getDadosReservas($request)['data'][0];
        $consumos = (object)$this->consumo_model->getDadosConsumos($request)['data'];
        $pagamentos = $this->pagamento_model->getDadosPagamentos($request)['data'];
        $diarias = (object)$this->consumo_model->getDadosDiarias($request)['data'];
        $dados->lista_diarias = $diarias;
        $dados->lista_consumos = $consumos;
        $dados->pagamentos = $pagamentos;
        $this->viewImpressao('nota_cliente',$dados);
    }   

    public function findParamByParam($param) {
        return $this->app_model->buscaParamByParam($param)[0];
    }

    public function relacao($params = null) {
        $this->active = "relatorio";     
        $this->viewImpressao($params, '');
    }

    // Site Admin

    public function bannerSite($request = null)
    {
        $this->active = "site";     
        $this->viewAdmin('Site/banner',$request,"");
    }

    public function cardSite($request = null)
    {
        $this->active = "site";     
        $this->viewAdmin('Site/cardApt',$request,"");
    }

    public function textSite($request = null)
    {
        $this->active = "site";     
        $this->viewAdmin('Site/textSite',$request,"");
    }

    public function colorSite($request = null)
    {
        $this->active = "site";     
        $this->viewAdmin('Site/colorSite',$request,"");
    }

    public function imagesSite($request = null)
    {
        $this->active = "site";     
        $this->viewAdmin('Site/imagesSite',$request,"");
    }

    public function configSite($request = null)
    {
        $this->active = "site";     
        $this->viewAdmin('Site/configSite',$request,"");
    }

}