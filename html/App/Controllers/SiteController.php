<?php

class SiteController extends Controller {

    protected $app_model;

    public function __construct() {
        $this->app_model = new AppModel();      
    }

    public function index() {
        $this->text = $this->findTextActive();
        $this->images = $this->findBannerActive();
        $this->cards = $this->findAptCardActive();
        $this->imagesSite = $this->findImagesActive();
        $this->viewSite('index');
        // $login = new LoginController();
        // return $login->index();
    }

    public function findAllBanner()
    {
        echo json_encode($this->app_model->buscaTodosBanners());
        return;
    } 

    public function findBannerById ($id)
    {
        echo json_encode($this->app_model->findBannerById($id));
        return;
    }
    private function findBannerActive() 
    {
        return $this->app_model->buscaBannersAtivos();
    }   
    
    public function saveBanner() 
    {
        $diretorio= __DIR__."../../Public/Site/Banner";
        $banner = self::uploadFileWithHash('imagem', $diretorio);
       
        if (is_null($banner)) {
            echo json_encode("Error uploading");
            return;
        }
        
        $create =  $this->app_model->createBanner($banner);

        echo json_encode($create);
    }

    public function updateBanner() 
    {
        $diretorio= __DIR__ . "/../../Public/Site/Banner";
        if (!empty($_FILES['imagem'])) {
            $cards = self::uploadFileWithHash('imagem', $diretorio);
       
            if (is_null($cards)) {
                echo json_encode("Error uploading");
                return;
            }

            if(isset($_POST['imagem_anterior'])) {
                $diretorio= __DIR__ . "/../../Public/Site/Banner";
                self::deleteFile($diretorio, $_POST['imagem_anterior']);
            }
        }
        $cards['id'] = $_POST['id'];
        
        $update = $this->app_model->updateBanner($cards);

        if(is_null($update) || $update) {
            $diretorio= __DIR__ . "/../../Public/Site/Banner";
            self::deleteFile($diretorio, $cards['imagem']);
        }

        echo json_encode($update);
    }

    public function findAllCardApt() 
    {
        echo json_encode($this->app_model->buscaTodosAptPromo());
        return;
    }

    private function findAptCardActive() 
    {
        return $this->app_model->buscaCardsAtivos();
    } 

    public function saveCardApt() 
    {
        $diretorio= __DIR__."../../Public/Site/Card_APT";
        $cards = self::uploadFileWithHash('imagem', $diretorio);
       
        if (is_null($cards)) {
            echo json_encode("Error uploading");
            return;
        }
        $cards['nome'] = $_POST['nome'];
        $cards['descricao'] = $_POST['descricao'];
        $cards['valor_atual'] = $_POST['valor_atual'];
        $cards['valor_anterior'] = $_POST['valor_anterior'];
        
        $create =  $this->app_model->createCardApt($cards);

        if(is_null($create) || $create) {
            $diretorio= __DIR__."/../../Public/Site/Card_APT";
            self::deleteFile($diretorio, $cards['imagem']);
        }

        echo json_encode($create);
    }

    public function updateCardApt() 
    {
        $diretorio= __DIR__ . "/../../Public/Site/Card_APT";
        if (!empty($_FILES['imagem'])) {
            $cards = self::uploadFileWithHash('imagem', $diretorio);
       
            if (is_null($cards)) {
                echo json_encode("Error uploading");
                return;
            }

            if(isset($_POST['imagem_anterior'])) {
                $diretorio= __DIR__. "/../../Public/Site/Card_APT";
                self::deleteFile($diretorio, $_POST['imagem_anterior']);
            }
        }
        $cards['nome'] = $_POST['nome'];
        $cards['descricao'] = $_POST['descricao'];
        $cards['valor_atual'] = $_POST['valor_atual'];
        $cards['valor_anterior'] = $_POST['valor_anterior'];
        $cards['id'] = $_POST['id'];
        
        $update =  $this->app_model->updateCardApt($cards);

        if(is_null($update) || $update) {
            $diretorio= __DIR__ . "/../../Public/Site/Card_APT";
            self::deleteFile($diretorio, $cards['imagem']);
        }

        echo json_encode($update);
    }

    public function findCardAPTById($id) 
    {
        echo json_encode($this->app_model->buscaCardById($id));
        return;
    }

    public function desativarCardAPTById($id) 
    {
        $res = $this->app_model->changeStatusCardById($id);
        if($res) {
            echo json_encode($res);
        }

        return;
    }

    public function findAllColor() 
    {
        echo json_encode($this->app_model->buscaTodosCores());
        return;
    }

    public function findColorByParams($params) 
    {
        echo json_encode($this->app_model->buscaColorByParams($params));
        return;
    }

    public function findColorById($id) 
    {
        echo json_encode($this->app_model->buscaColorById($id));
        return;
    }

    private function findColorActive() 
    {
        return $this->app_model->buscaColorAtivos();
    } 

    public function saveColor() 
    {       
        $create =  $this->app_model->createCor($_POST);
        if($create) {
            echo json_encode($create);
        }

        return;
    }

    public function updateColor($id) 
    {       
        $updated =  $this->app_model->updateCor($_POST, $id);
        if($updated) {
            echo json_encode($updated);
        }

        return;
    }

    public function findAllText() 
    {
        echo json_encode($this->app_model->buscaTodosTexto());
        return;
    }

    public function findTextByParams($params) 
    {
        echo json_encode($this->app_model->buscaTextoByParams($params));
        return;
    }

    public function findTextById($id) 
    {
        echo json_encode($this->app_model->buscaTextoById($id));
        return;
    }

    private function findTextActive() 
    {
        return $this->app_model->buscaTextoAtivos();
    } 

    public function saveText() 
    {       
        $create =  $this->app_model->createTexto($_POST);
        if($create) {
            echo json_encode($create);
        }

        return;
    }

    public function updateText($id) 
    {       
        $updated =  $this->app_model->updateTexto($_POST, $id);
        if($updated) {
            echo json_encode($updated);
        }

        return;
    }

    public function findAllImages() 
    {
        echo json_encode($this->app_model->buscaTodosImages());
        return;
    }

    public function findImagesByParams($params) 
    {
        echo json_encode($this->app_model->buscaImagesByParams($params));
        return;
    }

    public function findImagesById($id) 
    {
        echo json_encode($this->app_model->buscaImagesById($id));
        return;
    }

    private function findImagesActive() 
    {
        return $this->app_model->buscaImagesAtivos();
    } 

    public function saveImages() {
        $diretorio= __DIR__."../../Public/Site/Images";
        $cards = self::uploadFileWithHash('imagem', $diretorio);
       
        if (is_null($cards)) {
            echo json_encode("Error uploading");
            return;
        }
        
        $create =  $this->app_model->createImages($cards);

        if(is_null($create) || $create) {
            $diretorio= __DIR__ ."/../../Public/Site/Images";
            self::deleteFile($diretorio, $cards['imagem']);
        }

        echo json_encode($create);
    }

    public function updateImages($id) 
    {
        $cards = [];

        if (!empty($_FILES['imagem'])) {
            $diretorio= __DIR__ . "/../../Public/Site/Images";
            $cards = self::uploadFileWithHash('imagem', $diretorio);
       
            if (is_null($cards)) {
                echo json_encode("Error uploading");
                return;
            }

            if(isset($_POST['imagem_anterior'])) {
                $diretorio= __DIR__ . "../../Public/Site/Images";
                self::deleteFile($diretorio, $_POST['imagem_anterior']);
            }
        }

        if(empty($cards)) {
            return;
        }
        $cards['id'] = $id;
        $update =  $this->app_model->updateImages($cards);

        if(!$update) {
            $diretorio= __DIR__."/../../Public/Site/Images";
            self::deleteFile($diretorio, $cards['imagem']);
            
        return;
        }      

        echo json_encode($update);
    }

    public function findAllParam() {
        echo json_encode($this->app_model->buscaTodosParams());
        return;
    }

    public function findParamById($id) {
        echo json_encode($this->app_model->buscaParamById($id));
        return;
    }

    public function findParamByParam($param) {
        return $this->app_model->buscaParamByParam($param)[0] ?? [];
    }

    public function findColorByParam($param) { 
        return $this->app_model->findColorByParam($param)[0] ?? [];
    }

    public function saveParam () {
        $create =  $this->app_model->createParam($_POST);
        if($create) {
            echo json_encode($create);
        }

        return;
    }

    public function updateParam ($id) {
        $create =  $this->app_model->updateParam($_POST, $id);
        if($create) {
            echo json_encode($create);
        }

        return;
    }
}