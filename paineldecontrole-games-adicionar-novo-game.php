<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se a sessão atual não for de um administrador ou moderador, o usuário será redirecionado para a página inicial
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador'])){
        header('location: index.php');
    }

    // Arrays para armazenar mensagens de erro e sucesso.
    $mensagem_erro = [];
    $mensagem_sucesso = [];

    // Se o botão 'adicionar-novo-game' for clicado, tudo abaixo será executado.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar-novo-game'])){
        // Dados recebidos serão armazenados em sessões.
        $_SESSION['nome_game'] = addslashes(strip_tags(trim($_POST['nome-game'])));
        $_SESSION['imagem_game'] = addslashes(strip_tags(trim('imagens_games/'.$_FILES['imagem-game']['name'])));
        $_SESSION['nome_url_pagina'] = addslashes(strip_tags(trim($_POST['nome-pagina-url'])));
        $_SESSION['titulo_pagina'] = addslashes(strip_tags(trim($_POST['titulo-pagina'])));
        $_SESSION['categoria'] = addslashes(strip_tags(trim($_POST['categoria'])));
        $_SESSION['informacoes'] = trim($_POST['informacoes']);
        $_SESSION['produto_promocional'] = addslashes(strip_tags(trim(isset($_POST['produto-promocional']) ? $_POST['produto-promocional'] : "")));
        $_SESSION['lancamento'] = addslashes(strip_tags(trim(isset($_POST['lancamento']) ? $_POST['lancamento'] : "")));
        $_SESSION['preco_antigo'] = addslashes(strip_tags(trim($_POST['preco-antigo'])));
        $_SESSION['quantidade'] = addslashes(strip_tags(trim($_POST['quantidade'])));
        $_SESSION['preco_atual'] = addslashes(strip_tags(trim($_POST['preco-atual'])));

        // Se nenhuma imagem for escolhida, será mostrado uma mensagem de erro.
        if(!$_FILES['imagem-game']['name']){
            $mensagem_erro[] = "Escolha uma imagem";
        }else{
            // Se uma imagem for escolhida, tudo abaixo será executado.
            // Passando as dimensões da imagem para uma variável.
            // [0] = Largura da imagem.
            // [1] = Altura da imagem.
            $dimensoes_imagem = getimagesize($_FILES['imagem-game']['tmp_name']);
            $largura_imagem = $dimensoes_imagem[0];
            $altura_imagem = $dimensoes_imagem[1];
            
            // Se a imagem for menor que 300x300(LxA), será mostrado uma mensagem de erro.
            if($largura_imagem < 300 && $altura_imagem < 300){
                $mensagem_erro[] = "A imagem precisa ser maior que 300x300";
            }
        }

        // Passando o tipo da imagem para uma variável.
        $tipo_imagem = pathinfo($_SESSION['imagem_game'], PATHINFO_EXTENSION);

        // Se o tipo da imagem não for jpg, jpeg ou png, será mostrado uma mensagem de erro.
        if(!in_array($tipo_imagem, array('jpg', 'jpeg', 'png'))){
            $mensagem_erro[] = "O tipo da imagem precisa ser JPG, JPEG ou PNG.";
        }

        // Se o tamanho da imagem for igual a 0 ou maior que 500Kb, será mostrado uma mensagem de erro.
        if($_FILES['imagem-game']['size'] == 0 || $_FILES['imagem-game']['size'] > 500000){
            $mensagem_erro[] = "O tamanho da imagem precisa ser menor que 500Kb";
        }

        // Verificando se cada campo está vazio.
        if(strlen($_SESSION['nome_game']) == 0){
            $mensagem_erro[] = "Preencha o campo Nome do game.";
        }

        if(strlen($_SESSION['nome_url_pagina']) == 0){
            $mensagem_erro[] = "Preencha o campo Nome da página.";
        }

        if(strlen($_SESSION['titulo_pagina']) == 0){
            $mensagem_erro[] = "Preencha o campo Título da página.";
        }

        if(strlen($_SESSION['informacoes']) == 0){
            $mensagem_erro[] = "Preencha o campo Informações.";
        }

        if(empty($_SESSION['quantidade'])){
            $mensagem_erro[] = 'Preencha o campo Quantidade';
        }

        if(strlen($_SESSION['preco_atual']) == 0){
            $mensagem_erro[] = "Preencha o campo Preço atual.";
        }

        if(strlen($_SESSION['produto_promocional']) == 0){
            $mensagem_erro[] = "Escolha se o game é promocional ou não.";
        }

        if(strlen($_SESSION['lancamento']) == 0){
            $mensagem_erro[] = "Escolha se o game é lançamento ou não.";
        }

        // Se a opção de produto promocional escolhida foi '1', será verificado se o campo de preço antigo está vazio, se estiver, um erro vai aparecer.
        if($_SESSION['produto_promocional'] == 1){
            if(strlen($_SESSION['preco_antigo']) == 0){
                $mensagem_erro[] = "Preencha o campo Preço antigo.";
            }
        }

        // Se a categoria for igual a 'Escolha a categoria', que não é uma categoria válida, será mostrado uma mensagem de erro.
        if($_SESSION['categoria'] == 'Escolha a categoria'){
            $mensagem_erro[] = "Escolha uma categoria.";
        }

        // Verificando se o nome_url_pagina não tem nenhum espaço em branco.
        if(preg_match("/\s/", $_SESSION['nome_url_pagina'])){
            $mensagem_erro[] = "O Nome da página(URL) não pode conter nenhum espaço em branco";
        }

        // Se não houver nenhum erro, tudo abaixo será executado.
        if(count($mensagem_erro) == 0){
            // Verificando se o nome do game não existe no banco de dados, se for o caso, esse game poderá ser adicionado no banco de dados e ter uma página.
            if(!$dados->VerificarExistenciaGame("game-".$_SESSION['nome_url_pagina'])){
                // Armazenando a imagem numa pasta.
                copy($_FILES['imagem-game']['tmp_name'], $_SESSION['imagem_game']);

                // Se o produto não for promocional, ele será 0, então se for igual a 0, o preço antigo vai receber 0.
                if($_SESSION['produto_promocional'] == 0){
                    $_SESSION['preco_antigo'] = 0;
                }

                // Adicionando o game no banco de dados.
                $dados->AdicionarGame($_SESSION['nome_game'], $_SESSION['imagem_game'], "game-".$_SESSION['nome_url_pagina'], $_SESSION['titulo_pagina']." - SatuctGames", $_SESSION['categoria'], $_SESSION['informacoes'], '1', $_SESSION['produto_promocional'], $_SESSION['lancamento'], $_SESSION['quantidade'], $_SESSION['preco_atual'], $_SESSION['preco_antigo']);

                // Colocando o destino das páginas dos games numa variável.
                $url_pagina = fopen("games/game-$_SESSION[nome_url_pagina].php", "w");

                // Dados da página que será criada.
                $pagina_game_modelo = '<?php
// Esse script contém sessões de login, ex: adm, moderador, usuário.
include "../scripts/sessao_login.php";

// Script com métodos para várias coisas.
require_once "../scripts/dados.php";
$dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

// Buscando os dados do game pela seu nome da url e armazenando isso numa variável.
$game_info = $dados->BuscarDadosGame("game-'.$_SESSION['nome_url_pagina'].'");

// Verificando se existe alguém logado.
if(isset($info)){
    // Buscando as transações do usuário pelo seu id e armazenando isso numa variável.
    $transacao_info = $dados->BuscarTransacoesUsuario($info["id"]);

    // Criando um array, colocando todos os pk_id_game(id do jogo que ele comprou) e colocando nesse array.
    $id_game_transacao_usuario = [];
    foreach($transacao_info as $valor){
        $id_game_transacao_usuario[] = $valor["pk_id_game"];
    }
}else{
    // Se não tiver ninguém logado, o array vai receber 0.
    $id_game_transacao_usuario[] = 0;
}

// Array para armazenar os avisos para mostrar ao usuário.
$mensagem_aviso = [];

// Esse if só vai ser usado quando o botão comprar for clicado.
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comprar"])){
    // Se o usuário não estiver logado, será mostrado uma mensagem de erro para ele.
    if(!isset($_SESSION["id_administrador"]) && !isset($_SESSION["id_moderador"]) && !isset($_SESSION["id_usuario"])){
        $mensagem_erro_compra = "Você precisa está logado para realizar a compra";
    }else{
        // Se não, tudo abaixo será executado.
        // Criando sessões com informações do game dessa página.
        $_SESSION["nome_game"] = $game_info["nome_game"];
        $_SESSION["idgame"] = $game_info["id"];
        $_SESSION[$game_info["nome_url_pagina"]] = $game_info["preco_atual"];
        $_SESSION["quantidade"] = $_POST["quantidade"];
        $_SESSION["versao_game"] = $_POST["versao-game"];

        // Verificando se o campo de quantidade está vazio, se sim uma mensagem de erro será mostrada.
        if(empty($_SESSION["quantidade"])){
            $mensagem_erro = "Preencha o campo Quantidade";
        }

        // Se não existir mensagem de erro, o código abaixo será executado.
        if(!isset($mensagem_erro)){
            // Se a versão do game for digital, será criada uma sessão para armazenar o total a pagar, e outra para confirmar a compra/escolha do produto.
            // Logo após a isso o usuário será redirecionado para a página "finalizar-compra-digital".
            if($_SESSION["versao_game"] == "Digital"){
                $_SESSION["total_a_pagar"] = $game_info["preco_atual"];
                $_SESSION["compra"] = true;
                header("location: ../finalizar-compra-digital.php");
            }else{
                // Se a versão do game for física, será criada uma sessão para confirmar a compra/escolha do produto.
                // Logo após a isso o usuário será redirecionado para a página "finalizar-compra".
                $_SESSION["compra"] = true;
                header("location: ../finalizar-compra.php");   
            }
        }
    }
}

// Se o botão "adicionar-ao-carrinho" for clicado, tudo abaixo será executado.
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["adicionar-ao-carrinho"])){
    // Se a sessão "games_no_carrinho" não existir, ela será criada e terá o valor "0".
    if(!isset($_SESSION["games_no_carrinho"])){
        $_SESSION["games_no_carrinho"] = 0;
    }

    // Se não existir nenhuma dessas sessões, todas serão criadas.
    // Sessões para guardar dados do game, para usar nas próximas páginas.
    if(!isset($_SESSION["idgame_carrinho"]) && !isset($_SESSION["nome_game_carrinho"]) && !isset($_SESSION["quantidade_escolhida_carrinho"]) && !isset($_SESSION["quantidade_total_carrinho"]) && !isset($_SESSION["versao_game_carrinho"]) && !isset($_SESSION["imagem_game_carrinho"]) && !isset($_SESSION["preco_atual_carrinho"])){
        $_SESSION["idgame_carrinho"] = [];
        $_SESSION["nome_game_carrinho"] = [];
        $_SESSION["quantidade_escolhida_carrinho"] = [];
        $_SESSION["quantidade_total_carrinho"] = [];
        $_SESSION["versao_game_carrinho"] = [];
        $_SESSION["imagem_game_carrinho"] = [];
        $_SESSION["preco_atual_carrinho"] = [];
    }

    // Se não existir uma "key" igual o id do game na sessão array "idgame_carrinho", a sessão "games_no_carrinho" vai receber ele mesmo + 1.
    // Isso é para o número do lado do icone carrinho, quando o usuário adicionar o game ao carrinho, esse número vai receber +1, como não é possível adicionar o mesmo game no carrinho, essa condição vai evitar de adicionar +1 no número do carrinho.
    if(!array_key_exists($game_info["id"], $_SESSION["idgame_carrinho"])){
        $_SESSION["games_no_carrinho"]++;
    }
    
    // Se o nome do game não existir no array "nome_game_carrinho".
    if(!in_array($game_info["nome_game"], $_SESSION["nome_game_carrinho"])){
        // Todos os valores recebidos abaixo para cada sessão vai direto para uma key que é igual o id do game.
        $_SESSION["idgame_carrinho"][$game_info["id"]] = $game_info["id"];
        $_SESSION["nome_game_carrinho"][$game_info["id"]] = $game_info["nome_game"];
        $_SESSION["quantidade_escolhida_carrinho"][$game_info["id"]] = $_POST["quantidade"];
        $_SESSION["quantidade_total_carrinho"][$game_info["id"]] = $game_info["quantidade"];
        $_SESSION["versao_game_carrinho"][$game_info["id"]] = $_POST["versao-game"];
        $_SESSION["imagem_game_carrinho"][$game_info["id"]] = $game_info["imagem"];
        $_SESSION["preco_atual_carrinho"][$game_info["id"]] = $game_info["preco_atual"];

        // Se a sessão "total_carrinho_desconto" existir.
        if(isset($_SESSION["total_carrinho_desconto"])){
            // Será feito uma conversão de separador de número na sessão "preco_atual_carrinho" com o id do game sendo usado como key e atribuindo isso a uma variável.
            // Depois outra variável vai receber o valor inteiro da variável criada acima - mesmo valor * desconto do cupom / 100.
            $preco_atual_carrinho = str_replace(",", ".", $_SESSION["preco_atual_carrinho"][$game_info["id"]]);
            $preco_atual_desconto = $preco_atual_carrinho - ($preco_atual_carrinho * $_SESSION["desconto_cupom"] / 100);

            // Logo após tudo isso, a sessão abaixo vai receber ela mesma + o valor da variável "preco_atual_desconto".
            $_SESSION["total_carrinho_desconto"] = $_SESSION["total_carrinho_desconto"] + $preco_atual_desconto;
        }
    }else{
        // Se o nome do game existir no array "nome_game_carrinho", uma mensagem de erro vai aparecer, para avisar que não é possível adicionar o mesmo game no carrinho.
        $mensagem_erro_compra = "Você já adicionou esse game no carrinho.";
    }
}

// Esse if só vai ser usado quando o botão avaliar for clicado.
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["avaliar"])){
    // Se no array "id_game_transacao_usuario" existir um id igual o id do game, o código abaixo será executado.
    // Isso é para verificar se o usuário comprou o game dessa página.
    if(in_array($game_info["id"], $id_game_transacao_usuario)){
        // Pegando os dados recebidos e colocando em variáveis.
        $avaliacao = addslashes(strip_tags(trim($_POST["avaliacao"])));
        $classificacao = addslashes(strip_tags(trim($_POST["emoji"])));
        
        // Verificando se é um usuário logado.
        if(isset($_SESSION["id_usuario"])){

            // Inserindo a avaliação de acordo com o id do game e o id do usuário.
            if($dados->InserirAvaliacao($_SESSION["id_usuario"], $game_info["id"], $avaliacao, $classificacao)){
                header("refresh: 0");
            }

        }else if(isset($_SESSION["id_administrador"])){ // Verificando se é um administrador logado.

            // Inserindo a avaliação de acordo com o id do game e o id do administrador.
            if($dados->InserirAvaliacao($_SESSION["id_administrador"], $game_info["id"], $avaliacao, $classificacao)){
                header("refresh: 0");
            }
            
        }else if(isset($_SESSION["id_moderador"])){ // Verificando se é um moderador logado.

            // Inserindo a avaliação de acordo com o id do game e o id do moderador.
            if($dados->InserirAvaliacao($_SESSION["id_moderador"], $game_info["id"], $avaliacao, $classificacao)){
                header("refresh: 0");
            }
            
        }else{
            // Se não tiver ninguém logado, uma mensagem de aviso será mostrada para o usuário.
            $mensagem_aviso[] = "Para avaliar é preciso está logado.";
        }
    }else{
        // Se o usuário não tiver comprado o game dessa página, ele vai receber um aviso que é preciso ter esta compra para avaliar o game.
        $mensagem_aviso[] = "É preciso comprar este produto para avaliá-lo.";
    }
}

// Método para buscar as avaliações existentes para essa página.
$avaliacoes = $dados->BuscarAvaliacoes();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Essa loja é apenas um projeto experimental">
    <meta name="author" content="Gleydson José">
    <meta name="keywords" content="SatuctGames Satuct Games">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="../imagens/satuctgames-favicon.png?<?= time() ?>">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilo.css?<?= time() ?>">

    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- // BOOTSTRAP CSS -->

    <title><?= $game_info["titulo_pagina"] ?></title>
</head>
<body>
    <!-- HEADER -->
    <header class="container-fluid bg-cinza">
        <div class="row">
            <div class="col-6">
                <a href="../index.php">
                    <img src="../imagens/satuctgames-logo.png?<?= time() ?>" alt="Logotipo do site SatuctGames" class="img-fluid logotipo mx-2 my-4">
                </a>
            </div>
            <div class="col-6">
                <div class="text-right mt-2 mr-3">
                    <a href="https://www.facebook.com/" target="_blank" class="redes-sociais pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/" target="_blank" class="redes-sociais pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.youtube.com/?gl=BR&hl=pt" target="_blank" class="redes-sociais pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.instagram.com/" target="_blank" class="redes-sociais" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 px-0">
                <nav class="navbar navbar-expand-lg navbar-dark bg-preto menu-loja py-0">
                    <button class="navbar-toggler my-2" style="border-color: #dadada;" type="button" data-toggle="collapse" data-target="#menu-navegacao" aria-controls="menu-navegacao" aria-expanded="false" aria-label="menu navegacao">
                        <i class="fas fa-bars py-1 text-cinza"></i>
                    </button>

                    <div class="collapse navbar-collapse w-100" id="menu-navegacao">
                        <ul class="navbar-nav mt-lg-0 mt-2">
                            <li class="nav-item">
                                <a href="../index.php" class="nav-link btn-menu-cinza">Inicio</a>
                            </li>

                            <li class="nav-item">
                                <a href="../promocoes.php" class="nav-link btn-menu-cinza">Promoções</a>
                            </li>

                            <li class="nav-item dropdown">
                                <a href="" class="nav-link dropdown-toggle btn-menu-cinza" id="categorias-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">Categorias</a>
                                <div class="dropdown-menu" aria-labelledby="categorias-dropdown">
                                    <a href="../acao.php" class="dropdown-item">Ação</a>
                                    <a href="../aventura.php" class="dropdown-item">Aventura</a>
                                    <a href="../corrida.php" class="dropdown-item">Corrida</a>
                                    <a href="../luta.php" class="dropdown-item">Luta</a>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="../lancamentos.php" class="nav-link btn-menu-cinza">Lançamentos</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto">
                            <form class="form-inline my-lg-0 my-2" autocomplete="off">
                                <div class="form-busca d-none">
                                    <input class="form-control form-control-sm" id="buscar-game" type="text" placeholder="Escreva o nome do game" aria-label="Buscar" style="border: 1px solid #e4e2e2 !important;">
                                    <button class="btn btn-buscar-form" id="fechar-busca" type="button"><i class="fas fa-times btn-busca-icon"></i></button>

                                    <div class="busca-items text-break bg-preto shadow-sm" id="busca-items"></div>
                                </div>

                                <button class="btn btn-menu-cinza btn-abrir-busca" type="button"><i class="fas fa-search btn-busca-icon"></i></button>
                            </form>

                            <li class="nav-item">
                                <a href="../carrinho.php" class="nav-link btn-menu-cinza"><i class="fas fa-shopping-cart carrinho"><span class="ml-1"><?php
                                // Se houver games no carrinho, será mostrado a quantidade.
                                if(isset($_SESSION["games_no_carrinho"])){
                                    echo $_SESSION["games_no_carrinho"];
                                }else{
                                    // Se não, o valor será 0.
                                    echo "0";
                                }
                                ?></span></i></a>
                            </li>

                            <?php // Verificando se não tem ninguém logado
                            if(!isset($info)){ ?>
                            <li class="nav-item nav-usuario">
                                <a class="nav-link mt-lg-0 btn-menu-cinza" href="../login.php"><i class="fas fa-user mr-1"></i>Entrar</a>
                            </li>
                      <?php // Se tem alguém logado, será mostrado um menu próprio para esse usuário.
                            }else{ ?>
                            <li class="nav-item dropdown nav-usuario">
                                <a class="nav-link dropdown-toggle mt-lg-0 btn-menu-cinza" id="menu-usuario" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button" style="cursor: pointer;"><span class="text-cinza"><?= $info["nome"] ?></span></a>
                                <div class="dropdown-menu dropdown-menu-right mb-lg-2" aria-labelledby="menu-usuario">
                                    <a href="../detalhesdaconta.php" class="dropdown-item">Detalhes da conta</a>
                                <?php // Se a sessão atual for um administrador ou moderador, essa opção do menu será mostrada.
                                    if(isset($_SESSION["id_administrador"]) || isset($_SESSION["id_moderador"])){ ?>
                                        <a href="../paineldecontrole.php" class="dropdown-item">Painel de controle</a>
                              <?php } // ---------------------------------------- ?>
                                    <a href="../sair.php" class="dropdown-item">Sair</a>
                                </div>
                            </li>
                      <?php } ?>  
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <!-- // HEADER -->

    <!-- GAME -->
    <div class="container-fluid mt-5 py-5">
        <div class="row">
            <div class="col-12">
                <div class="row pb-5 mb-5 ml-2">
                    <div class="col-xl-3 col-lg-4 col-md-5 col-12 text-md-right">
                        <img src="../<?php 
                        // Se existe a imagem do game no banco de dados, ela será mostrada.
                        if(isset($game_info["imagem"])){ echo $game_info["imagem"]; } ?>" alt="Imagem do <?php 
                        // Se existe o nome do game no banco de dados, ele será mostrado.
                        if(isset($game_info["nome_game"])){ echo $game_info["nome_game"]; } ?>?<?= time() ?>" class="img-fluid rounded" style="filter: grayscale(20%);">
                    </div>

                    <div class="col-xl-9 col-lg-8 col-md-7 col-12 text-md-left mt-md-0 mt-4">
                        <h6 class="mb-3 text-break"><?php 
                        // Se existe o nome do game no banco de dados, ele será mostrado.
                        if(isset($game_info["nome_game"])){ echo $game_info["nome_game"]; } ?></h6>
                        <?php
                        // Se o game está disponível, será mostrado um aviso que ele está disponível, se não, será mostrado um aviso que não está.
                        if(isset($game_info["disponibilidade"])){
                            if($game_info["disponibilidade"] == 0){ ?>
                                <h6 class="text-danger"><i class="fas fa-exclamation-circle"></i> Indisponível</h6>
                      <?php }else{ ?>
                                <h6 class="text-success"><i class="fas fa-check-circle"></i> Disponível</h6>
                      <?php }
                        }
                        ?>

                        <?php
                        // Se o game está em promoção, será mostrado o valor antigo e o atual, se não, será mostrado apenas o atual.
                        if(isset($game_info["promocao"])){
                            if($game_info["promocao"] == 0){ ?>
                                <h5 class="text-preto mt-3">Por <?php if(isset($game_info["preco_atual"])){ echo $game_info["preco_atual"]; } ?></h5>
                      <?php }else{ ?>
                                <h6 class="text-muted mt-3">De <?php if(isset($game_info["preco_antigo"])){ echo $game_info["preco_antigo"]; } ?></h6>
                                <h5 class="text-preto">Por <?php if(isset($game_info["preco_atual"])){ echo $game_info["preco_atual"]; } ?></h5>
                      <?php }
                        }
                        ?>

                        <?php
                        // Se a quantidade do game for maior ou igual a 1, será mostrado todas as opções para o usuário, se não, essas opções não serão mostradas.
                        if(isset($game_info["quantidade"])){
                            if($game_info["quantidade"] >= 1){ ?>
                                <form method="POST">
                                    <h6 class="text-preto mt-3">Quantidade</h6>
                                    <input type="number" class="form-control form-control-sm form-control-alt-2 my-2" name="quantidade" id="desconto-criar-cupom" min="1" max="<?= $game_info["quantidade"] ?>" value="1" style="width: auto;">
                                    <?php
                                        // Se existir mensagem de erro, será mostrada aqui.
                                        if(isset($mensagem_erro)){ ?>
                                            <small class="form-text text-danger"><?= $mensagem_erro ?></small>
                                  <?php }
                                        // Se a quantidade for igual a 1, o texto será um, se for maior que 1, o texto será outro.
                                        if($game_info["quantidade"] == 1){ ?>
                                            <small class="form-text text-muted">Apenas 1 unidade disponível</small>
                                  <?php }else{ ?>
                                            <small class="form-text text-muted">Apenas <?= $game_info["quantidade"] ?> unidades disponíveis</small>
                                  <?php }
                                    ?>

                                    <h6 class="text-preto mt-3">Versão do game</h6>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="versao-game" id="versao-digital" value="Digital" checked>
                                        <label class="form-check-label" for="versao-digital">
                                            Digital
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="versao-game" id="versao-fisica" value="Física">
                                        <label class="form-check-label" for="versao-fisica">
                                            Física
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-cinza-form py-1 mr-1" name="comprar"><i class="fas fa-money-check-alt mr-2"></i>Comprar</button>
                                    <button type="submit" class="btn btn-cinza-form py-1" name="adicionar-ao-carrinho"><i class="fas fa-shopping-cart mr-2"></i>Adicionar ao carrinho</button>
                                </form>

                                <?php
                                    // Se existir alguma mensagem de erro de compra, será mostrada aqui.
                                    if(isset($mensagem_erro_compra)){ ?>
                                        <small class="form-text text-danger mt-2"><?= $mensagem_erro_compra ?></small>
                                <?php } ?>
                      <?php }
                        }
                        ?>

                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="row mb-5 py-1" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                            <div class="col-12">
                                <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">INFORMAÇÕES</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row pl-4">
                    <div class="col-12">
                        <?php
                            // Se as informações do game existe, elas serão mostradas.
                            if(isset($game_info["informacoes"])){
                                echo $game_info["informacoes"];
                            }
                        ?>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="row mb-5 py-1" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                            <div class="col-12">
                                <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">AVALIAÇÕES</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row pl-4">
                    <div class="col-12">
                        <form method="POST">
                            <div class="col-lg-7 col-md-9 col-12 px-0">
                                <div class="form-group">
                                    <label for="avaliacao"><h5>Deixe sua avaliação</h5></label>
                                    <textarea class="form-control form-control-alt-2" id="avaliacao" name="avaliacao" rows="4" maxlength="399" placeholder="Deixe sua avaliação sobre o game e nossa loja (Opcional)"></textarea>
                                </div>
                            </div>

                            <div class="col-lg-7 col-md-9 col-12 d-flex px-0">
                                <h6 class="mt-2">Classificação:</h6>

                                <div class="form-check mt-2 pl-3">
                                    <label class="form-check-label emoji-lbl" for="emoji-1">
                                        <input type="radio" class="form-check-input rating-check mx-0" id="emoji-1" value="1" name="emoji">
                                        <i class="far fa-angry"></i>
                                    </label>
    
                                    <label class="form-check-label ml-1 emoji-lbl" for="emoji-2">
                                        <input type="radio" class="form-check-input rating-check mx-0" id="emoji-2" value="2" name="emoji">
                                        <i class="far fa-frown"></i>
                                    </label>
    
                                    <label class="form-check-label ml-1 emoji-lbl" for="emoji-3">
                                        <input type="radio" class="form-check-input rating-check mx-0" id="emoji-3" value="3" name="emoji">
                                        <i class="far fa-meh"></i>
                                    </label>
    
                                    <label class="form-check-label ml-1 emoji-lbl" for="emoji-4">
                                        <input type="radio" class="form-check-input rating-check mx-0" id="emoji-4" value="4" name="emoji">
                                        <i class="far fa-smile"></i>
                                    </label>
    
                                    <label class="form-check-label ml-1 emoji-lbl" for="emoji-5">
                                        <input type="radio" class="form-check-input rating-check mx-0" id="emoji-5" value="5" name="emoji" checked>
                                        <i class="far fa-laugh"></i>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-cinza-form py-1 ml-auto" name="avaliar"><i class="fas fa-thumbs-up mr-2"></i>Avaliar</button>
                            </div>                      
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-7 col-md-9 col-12 my-5 ml-4">
                        <?php
                            // Se houver mensagens de aviso, elas serão mostradas na página
                            if(count($mensagem_aviso) == 1){?>
                                <div class="alert alert-amarelo d-flex text-break mb-5" role="alert">
                                    <span class="mr-3"><?php 
                                    foreach($mensagem_aviso as $aviso){
                                        echo "$aviso <br>";
                                    } ?></span>
                                    <button type="button" class="close ml-auto h-100" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                      <?php } ?>
                    </div>
                </div>

          <?php foreach($avaliacoes as $a){ // Mostrando as avaliações dos usuários ?>
              <?php if($a["pk_id_game"] == $game_info["id"]){ ?>
                    <div class="row mb-4">
                        <div class="col-lg-7 col-md-9 col-12 d-flex">
                            <h6 class="mb-3"><?= $a["nome_usuario"] ?></h6>

                            <div class="form-check pl-3">
                                <label class="form-check-label" for="emoji-1-user<?= $a["id"] ?>">
                                        <input type="radio" class="form-check-input rating-check-user mx-0" id="emoji-1-user<?= $a["id"] ?>" name="emoji-user<?= $a["id"] ?>" <?php
                                        // Se a classificação do usuário for 1, ela será marcada, se não, ela será desmarcada.
                                        if($a["classificacao"] == 1){
                                            echo "checked";
                                        }else{
                                            echo "disabled";
                                        }
                                        ?> style="cursor: default !important;">
                                        <i class="far fa-angry" style="cursor: default !important;"></i>
                                </label>

                                <label class="form-check-label ml-1" for="emoji-2-user<?= $a["id"] ?>">
                                        <input type="radio" class="form-check-input rating-check-user mx-0" id="emoji-2-user<?= $a["id"] ?>" name="emoji-user<?= $a["id"] ?>" <?php
                                        // Se a classificação do usuário for 2, ela será marcada, se não, ela será desmarcada.
                                        if($a["classificacao"] == 2){
                                            echo "checked";
                                        }else{
                                            echo "disabled";
                                        }
                                        ?> style="cursor: default !important;">
                                        <i class="far fa-frown" style="cursor: default !important;"></i>
                                </label>

                                <label class="form-check-label ml-1" for="emoji-3-user<?= $a["id"] ?>">
                                        <input type="radio" class="form-check-input rating-check-user mx-0" id="emoji-3-user<?= $a["id"] ?>" name="emoji-user<?= $a["id"] ?>" <?php
                                        // Se a classificação do usuário for 3, ela será marcada, se não, ela será desmarcada.
                                        if($a["classificacao"] == 3){
                                            echo "checked";
                                        }else{
                                            echo "disabled";
                                        }
                                        ?> style="cursor: default !important;">
                                        <i class="far fa-meh" style="cursor: default !important;"></i>
                                </label>

                                <label class="form-check-label ml-1" for="emoji-4-user<?= $a["id"] ?>">
                                        <input type="radio" class="form-check-input rating-check-user mx-0" id="emoji-4-user<?= $a["id"] ?>" name="emoji-user<?= $a["id"] ?>" <?php
                                        // Se a classificação do usuário for 4, ela será marcada, se não, ela será desmarcada.
                                        if($a["classificacao"] == 4){
                                            echo "checked";
                                        }else{
                                            echo "disabled";
                                        }
                                        ?> style="cursor: default !important;">
                                        <i class="far fa-smile" style="cursor: default !important;"></i>
                                </label>

                                <label class="form-check-label ml-1" for="emoji-5-user<?= $a["id"] ?>">
                                        <input type="radio" class="form-check-input rating-check-user mx-0" id="emoji-5-user<?= $a["id"] ?>" name="emoji-user<?= $a["id"] ?>" <?php
                                        // Se a classificação do usuário for 5, ela será marcada, se não, ela será desmarcada.
                                        if($a["classificacao"] == 5){
                                            echo "checked";
                                        }else{
                                            echo "disabled";
                                        }
                                        ?> style="cursor: default !important;">
                                        <i class="far fa-laugh" style="cursor: default !important;"></i>
                                </label>
                            </div>

                            <h6 class="text-muted ml-3"><?= (new DateTime($a["horario"]))->format("d/m/Y H:i:s") ?></h6>
                      <?php // Se a sessão de login atual for de um administrador ou de um moderador, será possível excluir uma avaliação.
                            if(isset($_SESSION["id_administrador"]) || isset($_SESSION["id_moderador"])){
                                // Se a sessão atual não for de um administrador e o id estrangeiro do usuário que está registrado na avalição for igual a 1, não será mostrado o botão excluir.
                                // Isso é para evitar do moderador excluir a avaliação do administrador.
                                if(!isset($_SESSION["id_administrador"]) && $a["pk_id_usuario"] == 1){
                                    echo "";
                                }else{ ?>
                                    <form method="POST" action="../scripts/excluir-avaliacao.php">
                                        <input type="radio" class="form-check-input d-none" name="nome_game" value="game-'.$_SESSION['nome_url_pagina'].'" checked>

                                        <button type="submit" class="btn btn-danger btn-sm ml-3" style="margin-top: -7px; padding: 0 5px !important;" name="id_avaliacao" value="<?= $a["id"] ?>"><i class="fas fa-times"></i></button>
                                    </form>
                          <?php }
                           } ?>
                        </div>

                        <div class="col-lg-7 col-md-9 col-12">
                            <p class="text-break text-muted"><?= $a["avaliacao"] ?></p>
                        </div>
                    </div>
              <?php } ?>
          <?php } ?>
            </div>
        </div>
    </div>
    <!-- // GAME -->

    <!-- FOOTER -->
    <div class="container-fluid bg-preto mt-5">
        <div class="row">
            <div class="col-6 mt-3">
                <h6 class="text-branco mt-0 mb-1 ml-1" style="font-size: 13pt;">Fale conosco</h6>
                <p class="text-branco my-0 ml-1" style="font-size: 12pt;">Celular: (00) 00000-0000</p>
                <p class="text-branco my-0 ml-1" style="font-size: 12pt;">Email: satuctgames@gmail.com</p>
                <a href="../faleconosco.php" class="btn-ver-detalhes my-0 ml-1" style="text-decoration: none; font-size: 11pt;">Envie mensagens por email para nós <i class="fas fa-arrow-left"></i></a>
            </div>

            <div class="col-6 text-right mt-3">
                <h6 class="text-branco mr-1" style="font-size: 13pt;">Redes sociais</h6>
                <div class="mr-1">
                    <a href="https://www.facebook.com/" target="_blank" class="redes-sociais-footer pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/" target="_blank" class="redes-sociais-footer pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.youtube.com/?gl=BR&hl=pt" target="_blank" class="redes-sociais-footer pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.instagram.com/" target="_blank" class="redes-sociais-footer" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-6 col-md-8 col-12">
                <h6 class="text-branco ml-1 mb-3" style="font-size: 13pt;">Formas de pagamento</h6>
                <img src="../imagens/boleto-footer.png" alt="" class="img-fluid ml-1">
                <img src="../imagens/pag-seguro-footer.png" alt="" class="img-fluid bg-branco rounded ml-2">
                <img src="../imagens/paypal-footer.png" alt="" class="img-fluid bg-branco rounded ml-2">
            </div>

            <div class="col-lg-6 col-md-4 col-12 text-md-right mt-md-0 ml-md-0 mt-5 ml-1">
                <h6 class="text-branco mr-1 mb-3" style="font-size: 13pt;">Forma de entrega</h6>
                <img src="../imagens/correios-footer.png" alt="" class="img-fluid bg-branco rounded mr-1">
            </div>
        </div>

        <div class="row">
            <h6 class="text-branco mx-auto mb-3 mt-5" style="font-size: 11pt;">SatuctGames Project 2019 - Todos os direitos reservados</h6>
        </div>
    </div>
    <!-- // FOOTER -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script>
        // Para pegar dados escrito na barra de busca e enviar para um script, nesse script será feito a busca do nome do game no banco de dados para depois retornar numa div.
        $("#buscar-game").keyup(function(){
            var buscar = $("#buscar-game").val();
            if(buscar.length > 0){

                $.ajax({
                    url: "../scripts/buscar_game_ajax.php",
                    type: "POST",
                    data: {buscar:buscar},
                    success: function(resposta){
                        $("#busca-items").css("display","block");
                        
                        $("#busca-items").html(resposta);
                    }
                })
            }else{
                $("#busca-items").css("display","none");
            }
        })
    </script>

    <script src="../js/script.js?<?= time() ?>"></script>
</body>
</html>';

                // Criando a página a partir da 'url_pagina(caminho da pasta que vai guardar as páginas)' e 'pagina_game_modelo(de onde vai vim os dados da página)'.
                fwrite($url_pagina, $pagina_game_modelo);

                // Removendo sessões que foram criadas para uso de tudo acima.
                unset($_SESSION['nome_game'], $_SESSION['imagem_game'], $_SESSION['nome_url_pagina'], $_SESSION['titulo_pagina'], $_SESSION['categoria'], $_SESSION['informacoes'], $_SESSION['produto_promocional'], $_SESSION['lancamento'], $_SESSION['preco_antigo'], $_SESSION['quantidade'], $_SESSION['preco_atual']);

                // Logo após tudo acima ser feito, uma mensagem de sucesso será mostrada.
                $mensagem_sucesso[] = "Game adicionado com sucesso.";

                // Atualizando a página em 5 segundos.
                echo "<meta HTTP-EQUIV='refresh' CONTENT='5;URL=paineldecontrole-games-adicionar-novo-game.php'>";
            }else{
                // Se já existir esse game no banco de dados, será mostrado uma mensagem de erro.
                $mensagem_erro[] = "Já existe um game com este nome";
            }
        }
    }
?>

</div>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Essa loja é apenas um projeto experimental">
    <meta name="author" content="Gleydson José">
    <meta name="keywords" content="SatuctGames Satuct Games">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="imagens/satuctgames-favicon.png?<?= time() ?>">
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.8.2/css/all.css' integrity='sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay' crossorigin='anonymous'>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css?<?= time() ?>">

    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- // BOOTSTRAP CSS -->

    <title>Painel de Controle Games - SatuctGames</title>
</head>
<body>
    <!-- HEADER -->
    <header class="container-fluid bg-cinza">
        <div class="row">
            <div class="col-6">
                <a href="index.php">
                    <img src="imagens/satuctgames-logo.png?<?= time() ?>" alt="Logotipo do site SatuctGames" class="img-fluid logotipo mx-2 my-4">
                </a>
            </div>
            <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                <h4 class="text-preto">Painel de Controle</h4>
                <h5 class="text-preto">Games</h5>
                <h6 style="color: #227cb9;"><?= $tag ?></h6>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 px-0">
                <nav class="navbar navbar-expand-lg navbar-dark bg-preto menu-loja py-0">
                    <button class="navbar-toggler my-2" style="border-color: #dadada;" type="button" data-toggle="collapse" data-target="#menu-navegacao" aria-controls="menu-navegacao" aria-expanded="false" aria-label="menu navegacao">
                        <i class="fas fa-bars py-1 text-cinza"></i>
                    </button>

                    <div class="collapse navbar-collapse w-100" id="menu-navegacao">
                        <ul class="navbar-nav mt-lg-0 mt-2">
                            <li class="nav-item">
                                <a href="index.php" class="nav-link btn-menu-cinza">Voltar para loja</a>
                            </li>

                            <li class="nav-item">
                                <a href="paineldecontrole-usuarios.php" class="nav-link btn-menu-cinza">Usuários</a>
                            </li>

                            <li class="nav-item">
                                <a href="paineldecontrole-games.php" class="nav-link btn-menu-cinza">Games</a>
                            </li>

                            <li class="nav-item">
                                <a href="paineldecontrole-cupons.php" class="nav-link btn-menu-cinza">Cupons</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto">
                        <?php // Se tem alguém logado
                            if(isset($info)){ ?>
                            <li class="nav-item dropdown nav-usuario">
                                <a class="nav-link dropdown-toggle mt-lg-0 btn-menu-cinza" id="menu-usuario" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button" style="cursor: pointer;"><span class="text-cinza"><?= $info['nome'] ?></span></a>
                                <div class="dropdown-menu dropdown-menu-right mb-lg-2" aria-labelledby="menu-usuario">
                                    <a href="detalhesdaconta.php" class="dropdown-item">Detalhes da conta</a>
                                <?php // Se a sessão atual for um administrador ou moderador, essa opção do menu será mostrada
                                    if(isset($_SESSION['id_administrador']) || isset($_SESSION['id_moderador'])){ ?>
                                        <a href="paineldecontrole.php" class="dropdown-item">Painel de controle</a>
                              <?php } // ---------------------------------------- ?>
                                    <a href="sair.php" class="dropdown-item">Sair</a>
                                </div>
                            </li>
                      <?php } ?> 
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <!-- // HEADER -->

    <!-- PAINEL DE CONTROLE GAMES - ADICIONAR NOVO GAME -->
    <div class="container-fluid mt-5 py-5">
            <div class="row">
                <div class="col-12">
                    <div class="row mb-3 py-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                        <div class="col-12">
                            <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">ADICIONAR NOVO GAME</span>
                        </div>
                    </div>

                    <div class="container-fluid mt-5 pt-4">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8 col-sm-9 col-11 mx-auto">
                                <form method="POST" enctype="multipart/form-data" autocomplete="off">
                                    <div class="form-group">
                                        <label for="nome-game-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-gamepad pr-2"></i>Nome do game</label>
                                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: Batman Arkham Origins" id="nome-game-adicionar-novo-produto" name="nome-game" <?php
                                        // Se existir uma sessão 'nome_game', será mostrado seu valor.
                                        if(isset($_SESSION['nome_game'])){ echo "value='".$_SESSION['nome_game']."'"; }
                                        ?>>
                                    </div>

                                    <div class="form-group">
                                        <label for="imagem-game-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-image pr-2"></i>Imagem do game</label>
                                        <input type="file" class="form-control-file" id="imagem-game-adicionar-novo-produto" name="imagem-game" accept="image/*">
                                        <span class="form-text text-cinza-2">(A imagem precisa ser maior que 300x300)</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="nome-pagina-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-file-alt pr-2"></i>Nome da página <span class="text-cinza-2">(URL)</span></label>
                                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: batman-arkham-origins" id="nome-pagina-adicionar-novo-produto" name="nome-pagina-url" <?php
                                        // Se existir uma sessão 'nome_url_pagina', será mostrado seu valor.
                                        if(isset($_SESSION['nome_url_pagina'])){ echo "value='".$_SESSION['nome_url_pagina']."'"; }
                                        ?>>
                                        <span class="form-text text-cinza-2">(o nome precisa ser todo minúsculo, e ter traços '-' no lugar do espaço)</span>
                                        <span class="text-danger">Fique ciente que não será possível alterar o nome da página futuramente</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="titulo-pagina-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-pencil-alt pr-2"></i>Título da página</label>
                                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: Batman Arkham Origins" id="titulo-pagina-adicionar-novo-produto" name="titulo-pagina" <?php
                                        // Se existir uma sessão 'titulo_pagina', será mostrado seu valor.
                                        if(isset($_SESSION['titulo_pagina'])){ echo "value='".$_SESSION['titulo_pagina']."'"; }
                                        ?>>
                                    </div>

                                    <label for="categoria-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-list-alt pr-2"></i>Categoria</label>
                                    <select class="form-control form-control-alt-2" id="categoria-adicionar-novo-produto" name="categoria">
                                        <option value="Escolha a categoria" <?php
                                        // Se a sessão categoria existir e o valor for igual a 'Escolha a categoria', esse option será selecionado.
                                        if(isset($_SESSION['categoria']) && $_SESSION['categoria'] == 'Escolha a categoria'){ 
                                            echo "selected";
                                        } 
                                        ?>>Escolha a categoria</option>
                                        <option value="Ação" <?php
                                        // Se a sessão categoria existir e o valor for igual a 'Ação', esse option será selecionado.
                                        if(isset($_SESSION['categoria']) && $_SESSION['categoria'] == 'Ação'){ 
                                            echo "selected";
                                        } 
                                        ?>>Ação</option>
                                        <option value="Aventura" <?php
                                        // Se a sessão categoria existir e o valor for igual a 'Aventura', esse option será selecionado.
                                        if(isset($_SESSION['categoria']) && $_SESSION['categoria'] == 'Aventura'){ 
                                            echo "selected";
                                        } 
                                        ?>>Aventura</option>
                                        <option value="Corrida" <?php
                                        // Se a sessão categoria existir e o valor for igual a 'Corrida', esse option será selecionado.
                                        if(isset($_SESSION['categoria']) && $_SESSION['categoria'] == 'Corrida'){ 
                                            echo "selected";
                                        } 
                                        ?>>Corrida</option>
                                        <option value="Luta" <?php
                                        // Se a sessão categoria existir e o valor for igual a 'Luta', esse option será selecionado.
                                        if(isset($_SESSION['categoria']) && $_SESSION['categoria'] == 'Luta'){ 
                                            echo "selected";
                                        } 
                                        ?>>Luta</option>
                                    </select>

                                    <div class="form-group mt-3">
                                        <label for="informacoes-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-sticky-note pr-2"></i>Informações</label>
                                        <textarea class="form-control form-control-alt-2" id="informacoes-adicionar-novo-produto" name="informacoes" rows="7" placeholder="ex: Sobre o jogo, Conteúdo adicional..."><?php
                                        // Se existir uma sessão 'informacoes', será mostrado seu valor.
                                        if(isset($_SESSION['informacoes'])){
                                             echo $_SESSION['informacoes'];
                                        }
                                        ?></textarea>
                                        <span class="form-text text-cinza-2">Ex de título para informação: <span><</span>h4<span>></span>Título<span><</span>/h4<span>></span> </span>
                                        <span class="form-text text-cinza-2">Ex de informação: <span><</span>p class="text-break text-muted mb-5"<span>></span>Informação<span><</span>/p<span>></span></span>
                                        <span class="form-text text-cinza-2">É obrigatório usar o título e informação com as tags acima.</span>
                                    </div>

                                    <div class="form-group">
                                        <h5 class="text-preto mb-2">Produto promocional?</h5>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="produto-promocional" id="produto-promocional-sim" value="1" <?php
                                            // Se a sessão 'produto_promocional' existir e for igual a '1', esse radio será marcado.
                                            if(isset($_SESSION['produto_promocional']) && $_SESSION['produto_promocional'] == 1){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="produto-promocional-sim">
                                                Sim
                                            </label>
                                        </div>
                
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="produto-promocional" id="produto-promocional-nao" value="0" <?php
                                            // Se a sessão 'produto_promocional' existir e for igual a 0, esse radio será marcado.
                                            if(isset($_SESSION['produto_promocional']) && $_SESSION['produto_promocional'] == 0){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="produto-promocional-nao">
                                                Não
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <h5 class="text-preto mb-2">Lançamento?</h5>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="lancamento" id="lancamento-sim" value="1" <?php
                                            // Se a sessão 'lancamento' existir e for igual a '1', esse radio será marcado.
                                            if(isset($_SESSION['lancamento']) && $_SESSION['lancamento'] == 1){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="lancamento-sim">
                                                Sim
                                            </label>
                                        </div>
                
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="lancamento" id="lancamento-nao" value="0" <?php
                                            // Se a sessão 'lancamento' existir e for igual a '0', esse radio será marcado.
                                            if(isset($_SESSION['lancamento']) && $_SESSION['lancamento'] == 0){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="lancamento-nao">
                                                Não
                                            </label>
                                        </div>
                                    </div>

                                    <div <?php
                                        // Se existir uma sessão 'produto_promocional' e ela for '1', o preço antigo será mostrado, se não for o caso, o preço antigo vai ficar escondido.
                                        if(isset($_SESSION['produto_promocional']) && $_SESSION['produto_promocional'] == 1){
                                            echo "class='form-group preco-antigo'";
                                        }else{
                                            echo "class='form-group preco-antigo d-none'";
                                        }
                                    ?>>
                                        <label for="preco-antigo-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-dollar-sign pr-2"></i>Preço antigo</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text" id="preco-antigo-adicionar-novo-produto">R$</span>
                                            </div>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: 69,99" id="preco-antigo-adicionar-novo-produto" name="preco-antigo" <?php
                                            // Se a sessão 'preco_antigo' existir, será mostrado o seu valor.
                                            if(isset($_SESSION['preco_antigo'])){
                                                echo  "value='".$_SESSION['preco_antigo']."'";;
                                            }
                                            ?>>
                                        </div>
                                    </div>
                
                                    <div class="form-row">
                                        <div class="form-group col-6">
                                            <label for="quantidade-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-cubes pr-2"></i>Quantidade</label>
                                            <input type="number" class="form-control form-control-alt-2" name="quantidade" id="quantidade-adicionar-novo-produto" min="1" <?php
                                            // Se a sessão quantidade existir, será mostrado o seu valor, se não, o valor será 1.
                                            if(isset($_SESSION['quantidade'])){
                                                echo "value='$_SESSION[quantidade]'";
                                            }else{
                                                echo "value='1'";
                                            }
                                            ?>>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="preco-atual-adicionar-novo-produto" class="text-cinza-2"><i class="fas fa-dollar-sign pr-2"></i>Preço atual</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text" id="preco-atual-adicionar-novo-produto">R$</span>
                                                </div>
                                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: 99,99" id="preco-atual-adicionar-novo-produto" name="preco-atual" <?php
                                                // Se a sessão 'preco_atual' existir, será mostrado o seu valor.
                                                if(isset($_SESSION['preco_atual'])){
                                                    echo  "value='".$_SESSION['preco_atual']."'";;
                                                }
                                                ?>>
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="btn-group w-100 mt-4">
                                        <a href="paineldecontrole-games.php" class="btn btn-azul-form w-25 py-2"><i class="fas fa-caret-square-left pr-2"></i>Voltar</a>
                                        <button type="submit" class="btn btn-cinza-form w-75 py-2 ml-2" name="adicionar-novo-game"><i class="fas fa-plus-circle pr-2"></i>Adicionar novo game</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                
                        <div claass="row">
                            <div class="col-xl-5 col-md-7 col-11 mx-auto mt-5">
                            <?php 
                            // Se houver mensagens de erro, elas serão mostradas na página
                            if(count($mensagem_erro) > 0){ ?>
                                <div class="alert alert-danger d-flex text-break mb-5" role="alert">
                                    <span class="mr-3"><?php foreach($mensagem_erro as $erro){ echo "$erro <br>"; } ?></span>
                                    <button type="button" class="close ml-auto h-100" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                      <?php } ?>
                            <?php
                            // Se houver a mensagem de sucesso, ela será mostrada na página
                            if(count($mensagem_sucesso) == 1){?>
                                <div class="alert alert-success d-flex text-break mb-5" role="alert">
                                    <span class="mr-3"><?php foreach($mensagem_sucesso as $sucesso){ echo "$sucesso <br>"; } ?></span>
                                    <button type="button" class="close ml-auto h-100" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                      <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- // PAINEL DE CONTROLE GAMES - ADICIONAR NOVO GAME -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>