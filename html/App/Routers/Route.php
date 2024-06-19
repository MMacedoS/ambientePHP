<?php

class Route {
    protected string $controller = 'SiteController';
    protected string $method = 'index';
    protected array $parameters = [];

    public function __construct(){
        $this->prepareUrl();
    }

    public function prepareUrl() {
        $url = filter_input(INPUT_GET, 'pag', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $urlParts = !empty($url) ? explode('/', $url) : [];

        if (empty($urlParts)) {            
            return;
        }

        $controllerName = $urlParts[0] . 'Controller';
   
        $controllerPath = $this->getControllerPath($controllerName);
        
        if (!empty($controllerPath)) {
            $this->controller = $controllerName;

            if (count($urlParts) > 1) {
                array_shift($urlParts);

                $this->method = isset($urlParts[0]) ? $this->kebabToCamelCase($urlParts[0]) : 'index';
                array_shift($urlParts);

                $this->parameters = $urlParts;                
            }
            return;            
        }
        // Redirecionar para a página de login
        $this->controller = 'SiteController';
        $this->method = 'index';
    }

    public function run()
    {
        if ($this->isRouteValid()) {
            $controller = new $this->controller;
            if (method_exists($controller, $this->method)) {             
                call_user_func_array([$controller, $this->method], $this->parameters);
            }

            return;
        }
        include(__DIR__ . '/../../View/404/404.php');
        exit;
    }

    private function isRouteValid()
    {
        $allowedControllers = [
            'SiteController', 
            'LoginController',
            'AdministrativoController',
            'AutorController',
            'ApartamentoController',
            'ConsultaController',
            'ConsumoController',
            'DiariaController',
            'FinanceiroController',
            'FuncionarioController',
            'HospedeController',
            'ImpressaoController',
            'PagamentoController',
            'ProdutoController',
            'ReservaController',
            'VendasController',
            'ApagadosController'
        ];

        $allowedMethods = [
            'index',
            'logar',
            'logouf',
            'venda',
            'apartamentos',
            'buscaApartamentos',
            'funcionarios',
            'buscaFuncionarios',
            'hospedes',
            'buscaHospedes',
            'reservas',
            'consultas',
            'mapas',
            'buscaReservas',
            'hospedadas',
            'buscaHospedadas',
            'checkin',
            'financeiro',
            'buscaCheckin',
            'listproduto',
            'listApartamento',
            'checkout',
            'disponiveis',
            'buscaCheckout',
            'confirmada',
            'buscaConfirmada',
            'produtos',
            'buscaProdutos',
            'buscaEntradaProdutos',
            'entradaEstoque',
            'estoque',
            'buscaEstoques',
            'buscaEntradaEstoques',
            'buscaProdutosSelect',
            'movimentacoes',
            'buscaMovimentos',
            'entrada',
            'buscaEntrada',
            'saida',
            'buscaSaida',
            'cliente',
            'findParamByParam',
            'relacao',
            'bannerSite',
            'cardSite',
            'textSite',
            'colorSite',
            'imagesSite',
            'configSite',
            'getAll',
            'buscaApartamentos',
            'buscaApartamentoPorId',
            'salvarApartamentos',
            'atualizarApartamentos',
            'changeStatusApartamentos',
            'hospedadas',
            'checkin',
            'checkout',
            'confirmada',
            'reservada',
            'mapa',
            'addConsumo',
            'updateConsumo',
            'getConsumoPorId',
            'getDadosConsumos',
            'getRemoveConsumo',
            'addDiaria',
            'getDiariaPorId',
            'buscaMovimentos',
            'buscaEntrada',
            'findAllEntradas',
            'findEntradasByParams',
            'findEntradaById',
            'deleteEntradaById',
            'buscaSaida',
            'findAllSaidas',
            'insertSaida',
            'findSaidasByParams',
            'salvarEntradas',
            'deleteSaidaById',
            'atualizarEntradas',
            'findMovimentosByParams',
            'findMovimentos',
            'findAllPagamento',
            'findPagamentoByParams',
            'buscaFuncionarios',
            'buscaFuncionarioPorId',
            'salvarFuncionarios',
            'atualizarFuncionarios',
            'changeStatusFuncionarios',
            'getAllSelect',
            'buscaHospedes',
            'buscaHospedePorId',
            'salvarHospedes',
            'atualizarHospedes',
            'changeStatusHospedes',
            'addPagamento',
            'getDadosPagamentos',
            'getRemovePagamento',
            'getDadosProdutos',
            'buscaProduto',
            'buscaProdutoPorId',
            'salvarProdutos',
            'atualizarProduto',
            'changeStatusProduto',
            'buscaEntradaProduto',
            'buscaProdutos',
            'buscaEstoques',
            'salvarEntrada',
            'deleteEntrada',
            'buscaReservaPorId',
            'salvarReservas',
            'atualizarReserva',
            'changeStatusReservas',
            'reservaBuscaPorData',
            'executaCheckout',
            'changeCheckinReservas',
            'getDadosReservas',
            'getDadosDiarias',
            'getDiariasPorId',
            'updateDiaria',
            'getRemoveDiarias',
            'gerarDiarias',
            'findAllCafe',
            'findAllReservas',
            'findReservasByParams',
            'findAllVendas',
            'addVenda',
            'getVendasItems',
            'getItensPorId',
            'updateItensById',
            'addItensByIdVenda',
            'deleteItensById',
            'updateVendas',
            'deleteVendas',
            'findAllParam',
            'findAllBanner',
            'updateParam',
            'findBannerActive',
            'saveBanner',
            'updateBanner',
            'findAllCardApt',
            'findAptCardActive',
            'saveCardApt',
            'updateCardApt',
            'findCardAPTById',
            'desativarCardAPTById',
            'findAllColor',
            'findColorByParams',
            'findColorById',
            'saveColor',
            'updateColor',
            'findAllText',
            'findTextByParams',
            'findTextById',
            'findTextActive',
            'saveText',
            'updateText',
            'findAllImages',
            'findImagesByParams',
            'findImagesById',
            'saveImages',
            'updateImages',
            'findAllParam',
            'findParamById',
            'findParamByParam',
            'findColorByParam',
            'saveParam',
            'findBannerById',
            'apagados',
            'findAllApagados',
            'changeStatusApagados',
            'findById',
            'changeAllStatusApagados',
            'notaClienteReserva',
            'buscaAllReservaPorId',
            'getAllNota',
            'atualizaDiariaNotExists'
        ];

        $controllerPath = $this->getControllerPath($this->controller);

        if (file_exists($controllerPath)) {
            return in_array($this->controller, $allowedControllers) && in_array($this->method, $allowedMethods);
        }
        
        if (!file_exists($controllerPath)) {
            // Redirecionar para página de erro 404
            http_response_code(404);
            include(__DIR__ . '/../../View/404/404.php');
            exit;
        }

        return false;
    }

    private function getControllerPath($controllerName)
    {
        $controllerPaths = [
            __DIR__ . '/../Controllers/' . $controllerName . '.php',
            __DIR__ . '/../Controllers/Admin/' . $controllerName . '.php',
            __DIR__ . '/../Controllers/API/' . $controllerName . '.php'
        ];


        foreach ($controllerPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return '';
    }

    function kebabToCamelCase($string) {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $string))));
    }
}