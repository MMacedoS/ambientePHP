<?php

class AppModel extends ConexaoModel {

    protected $conexao;

    public function __construct() 
    {
        $this->conexao = ConexaoModel::conexao();
    }

    public function buscaCardById($id) 
    {   
        if (is_null($id)) return null;

        $cmd = $this->conexao->prepare(
            "SELECT * FROM apt_card_promo WHERE id = $id ORDER BY id ASC"
        );

        $cmd->execute();
        return $cmd->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaTodosBanners() 
    {
        $cmd = $this->conexao->query(
            "SELECT 
                id,
                nome_original,
                status,
                created_at
            FROM
                banner 
            order by id desc"
        );
        return $cmd->fetchAll();
    }

    public function findBannerById($id) 
    {
        $cmd = $this->conexao->query(
            "SELECT 
                *
            FROM
                banner 
            WHERE id = $id 
            order by id desc"
        );
        return $cmd->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaTodosAptPromo() 
    {
        $cmd = $this->conexao->query(
            "SELECT 
                id,
                nome,
                valor_atual,
                valor_anterior,
                status,
                created_at
            FROM
                apt_card_promo
            order by id desc"
        );
        return $cmd->fetchAll();
    }

    public function buscaBannersAtivos() 
    {
        $cmd = $this->conexao->query(
            "SELECT 
                imagem
            FROM
                banner
            WHERE
                status = 1
            order by id desc"
        );
        return $cmd->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaCardsAtivos() 
    {
        $cmd = $this->conexao->query(
            "SELECT 
                imagem,
                nome,
                valor_atual,
                valor_anterior,
                descricao
            FROM
                apt_card_promo
            WHERE
                status = 1
            order by id desc"
        );
        return $cmd->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createBanner($params) {
        if(is_null($params)) {
            return null;
        }

        $cmd = $this->conexao->prepare(
            "INSERT INTO banner set imagem = :imagem, nome_original = :nome_original, status = :status"
        );

        $cmd->bindValue(':imagem', $params['imagem']);
        $cmd->bindValue(':nome_original', $params['nome_original']); 
        $cmd->bindValue(':status', $params['status']);
        $cmd->execute();

        return $cmd->fetchAll();
    }

    public function updateBanner($params) {
        if(is_null($params)) {
            return null;
        }

        $cmd = $this->conexao->prepare(
            "UPDATE banner set imagem = :imagem, nome_original = :nome_original, status = :status WHERE id = :id"
        );

        $cmd->bindValue(':imagem', $params['imagem']);
        $cmd->bindValue(':nome_original', $params['nome_original']); 
        $cmd->bindValue(':status', $params['status']);
        $cmd->bindValue(':id', $params['id']);
        $cmd->execute();

        return $cmd->fetchAll();
    }

    public function createCardApt($params) {
        if(is_null($params)) {
            return null;
        }

       try {
            $cmd = $this->conexao->prepare(
                "INSERT INTO apt_card_promo 
                set 
                    imagem = :imagem, 
                    nome_original = :nome_original, 
                    status = :status,
                    nome = :nome, 
                    valor_atual = :valor_atual,
                    valor_anterior = :valor_anterior,
                    descricao = :descricao"
            );

            $cmd->bindValue(':imagem', $params['imagem']);
            $cmd->bindValue(':nome_original', $params['nome_original']); 
            $cmd->bindValue(':status', $params['status']);
            $cmd->bindValue(':nome', $params['nome']);
            $cmd->bindValue(':valor_atual', $params['valor_atual']);
            $cmd->bindValue(':valor_anterior', $params['valor_anterior']);
            $cmd->bindValue(':descricao', $params['descricao']);
            $cmd->execute();

            if($cmd) {
                return true;
            }
       } catch (\Throwable $th) {
            return null;
       }
    }

    public function updateCardApt($params) {
        if(is_null($params)) {
            return null;
        }

       try {
            $cmd = $this->conexao->prepare(
                "UPDATE apt_card_promo 
                set 
                    nome = :nome, 
                    valor_atual = :valor_atual,
                    valor_anterior = :valor_anterior,
                    descricao = :descricao 
                WHERE 
                    id = :id"
            );
            
            $cmd->bindValue(':nome', $params['nome']);
            $cmd->bindValue(':valor_atual', $params['valor_atual']);
            $cmd->bindValue(':valor_anterior', $params['valor_anterior']);
            $cmd->bindValue(':descricao', $params['descricao']);
            $cmd->bindValue(':id', $params['id']);
            $cmd->execute();

            if(isset($params['imagem'])) {     
                $cmd = $this->conexao->prepare(
                    "UPDATE apt_card_promo 
                    set 
                        imagem = :imagem, 
                        nome_original = :nome_original, 
                        status = :status
                    WHERE 
                        id = :id"
                );           
                $cmd->bindValue(':imagem', $params['imagem']);
                $cmd->bindValue(':nome_original', $params['nome_original']); 
                $cmd->bindValue(':status', $params['status']);
                $cmd->bindValue(':id', $params['id']);
                $cmd->execute();
            }

            if($cmd) {
                return true;
            }
       } catch (\Throwable $th) {
            return null;
       }
    }

    public function changeStatusCardById($id) 
    {
        $cards = $this->buscaCardById($id);
        
        if (is_null($cards)) {
            return false;
        }

        $cmd = $this->conexao->prepare(
            "UPDATE apt_card_promo SET status = :status WHERE id = :id"
        );
        $cmd->bindValue(':status', $cards[0]['status'] == 1 ? 0 : 1);
        $cmd->bindValue(':id', $id);
        $cmd->execute();

        if($cmd) {
            return true;
        }

        return false;
    }

    public function buscaTodosCores() 
    {
        $cmd = $this->conexao->query(
            'SELECT * FROM layout_color_site'
        );

        return $cmd->fetchAll();
    }

    public function createCor($params) 
    {
        if (is_null($params)) {
            return false;
        }

        $cmd = $this->conexao->prepare(
            "INSERT INTO layout_color_site set status = :status, name = :name, color = :color"
        );
        $cmd->bindValue(':color', $params['color']);
        $cmd->bindValue(':name', $params['name']);
        $cmd->bindValue(':status', 1);
        $cmd->execute();

        if($cmd) {
            return true;
        }

        return false;
    }

    public function updateCor($params, $id) 
    {
        if (is_null($params)) {
            return false;
        }

        $cmd = $this->conexao->prepare(
            "UPDATE layout_color_site set status = :status, name = :name, color = :color WHERE id = :id"
        );
        $cmd->bindValue(':color', $params['color']);
        $cmd->bindValue(':name', $params['name']);
        $cmd->bindValue(':status', 1);
        $cmd->bindValue(':id', $id);
        $cmd->execute();

        if($cmd) {
            return true;
        }

        return false;
    }

    public function buscaColorByParams($cor) 
    {       
        $sql  = "SELECT 
             *
        FROM
            layout_color_site
        WHERE
            name LIKE '%$cor%'";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function buscaColorById ($id) {
        $sql  = "SELECT 
                *
        FROM
            layout_color_site
        WHERE
            id = $id";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function buscaColorAtivos () {
        $sql  = "SELECT 
                name, color
        FROM
            layout_color_site
        WHERE
            status = 1";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function buscaTodosTexto() 
    {
        $cmd = $this->conexao->query(
            'SELECT * FROM texto_site'
        );

        return $cmd->fetchAll();
    }

    public function buscaTextoByParams($texto) 
    {
        $sql  = "SELECT 
        *
        FROM
            texto_site
        WHERE
            texto LIKE '%$texto%'";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function buscaTextoById($id) 
    {
        $sql  = "SELECT 
        *
        FROM
            texto_site
        WHERE
            id = $id";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function buscaTextoAtivos() 
    {
        $sql  = "SELECT 
            texto
        FROM
            texto_site
        WHERE
            status = 1";

        $cmd = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function createTexto($params) 
    {
        if (is_null($params)) {
            return false;
        }

        $cmd = $this->conexao->prepare(
            "INSERT INTO texto_site set status = :status, texto = :texto, sessao = :sessao"
        );
        $cmd->bindValue(':sessao', $params['sessao']);
        $cmd->bindValue(':texto', $params['texto']);
        $cmd->bindValue(':status', 1);
        $cmd->execute();

        if($cmd) {
            return true;
        }

        return false;
    }

    public function updateTexto($params, $id) 
    {
        if (is_null($params)) {
            return false;
        }

        $cmd = $this->conexao->prepare(
            "UPDATE texto_site set status = :status, texto = :texto, sessao = :sessao WHERE id = :id"
        );
        $cmd->bindValue(':sessao', $params['sessao']);
        $cmd->bindValue(':texto', $params['texto']);
        $cmd->bindValue(':status', 1);
        $cmd->bindValue(':id', $id);
        $cmd->execute();

        if($cmd) {
            return true;
        }

        return false;
    }

    public function buscaTodosImages() 
    {
        $sql  = "SELECT 
            *
        FROM
            imagem_site";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function buscaImagesByParams($image) 
    {
        $sql  = "SELECT 
        *
        FROM
            imagem_site 
        WHERE
            imagem LIKE '%$image%'
        ";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function buscaImagesById($id) 
    {
        $sql  = "SELECT 
        *
        FROM
            imagem_site 
        WHERE
            id = $id
        ";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function buscaImagesAtivos() 
    {
        $sql  = "SELECT 
        *
        FROM
            imagem_site 
        WHERE
            status = 1
        ";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }
 
    public function createImages($params) {
        if(is_null($params)) {
            return null;
        }

       try {
            $cmd = $this->conexao->prepare(
                "INSERT INTO imagem_site 
                set 
                    imagem = :imagem, 
                    nome_original = :nome_original, 
                    status = :status"
            );

            $cmd->bindValue(':imagem', $params['imagem']);
            $cmd->bindValue(':nome_original', $params['nome_original']); 
            $cmd->bindValue(':status', $params['status']);
            $cmd->execute();

            if($cmd) {
                return true;
            }
       } catch (\Throwable $th) {
            return null;
       }
    }

    public function updateImages($params) {
        if(is_null($params)) {
            return null;
        }

       try {
            $cmd = $this->conexao->prepare(
                "UPDATE imagem_site 
                set 
                    imagem = :imagem, 
                    nome_original = :nome_original, 
                    status = :status
                WHERE id = :id"
            );

            $cmd->bindValue(':imagem', $params['imagem']);
            $cmd->bindValue(':nome_original', $params['nome_original']); 
            $cmd->bindValue(':status', $params['status']);
            $cmd->bindValue(':id', $params['id']);
            $cmd->execute();

            if($cmd) {
                return true;
            }
       } catch (\Throwable $th) {
            return null;
       }
    }

    public function buscaTodosParams() 
    {
        $sql  = "SELECT 
        *
        FROM
            configuracao 
        ";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }

    public function createParam($params) 
    {
        if (is_null($params)) {
            return false;
        }

        $cmd = $this->conexao->prepare(
            "INSERT INTO configuracao set valor = :valor, parametro = :parametro"
        );
        $cmd->bindValue(':valor', $params['valor']);
        $cmd->bindValue(':parametro', $params['parametro']);
        $cmd->execute();

        if($cmd) {
            return true;
        }

        return false;
    }

    public function updateParam($params, $id) 
    {
        if (is_null($params)) {
            return false;
        }

        $cmd = $this->conexao->prepare(
            "UPDATE configuracao set valor = :valor, parametro = :parametro where id = :id"
        );
        $cmd->bindValue(':valor', $params['valor']);
        $cmd->bindValue(':parametro', $params['parametro']);
        $cmd->bindValue(':id', $id);
        $cmd->execute();

        if($cmd) {
            return true;
        }

        return false;
    }

    public function buscaParamById($id) 
    {
        $sql  = "SELECT 
        *
        FROM
            configuracao 
        WHERE
            id = $id
        ";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll();
        }

        return [];
    }


    public function buscaParamByParam ($params) 
    {
        $sql  = "SELECT 
        *
        FROM
            configuracao 
        WHERE
            parametro = '$params'
        ";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public function findColorByParam ($params) 
    {
        $sql  = "SELECT 
            color
        FROM
            layout_color_site 
        WHERE
            name = '$params'
        ";

        $cmd  = $this->conexao->query(
            $sql
        );

        if($cmd->rowCount() > 0)
        {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }
}