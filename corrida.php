<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Script com métodos para várias coisas.
    require_once 'scripts/dados.php';
    $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // Variáveis para inicialização da paginação.
    $pagina_atual = 0;
    $game_inicial_pagina = 0;
    $games_por_pagina = 8;

    // Se o botão 'anterior' for clicado, ele retornará o valor de $pagina_anterior para passar para $pagina_atual, e $game_inicial_pagina_anterior para $game_inicial_pagina.
    if($_SERVER['REQUEST_METHOD'] && isset($_POST['anterior'])){
        $pagina_atual = $_POST['pagina_atual'];
        $game_inicial_pagina = $_POST['game_inicial_pagina'];
    }

    // Se o botão 'proximo' for clicado, ele retornará o valor de $proxima_pagina para passar para $pagina_atual, e $game_inicial_proxima_pagina para $game_inicial_pagina.
    if($_SERVER['REQUEST_METHOD'] && isset($_POST['proximo'])){
        $pagina_atual = $_POST['pagina_atual'];
        $game_inicial_pagina = $_POST['game_inicial_pagina'];
    }

    // Variáveis para voltar e avançar páginas.
    $pagina_anterior = $pagina_atual - 1;
    $proxima_pagina = $pagina_atual + 1;
    $game_inicial_pagina_anterior = $game_inicial_pagina - 8;
    $game_inicial_proxima_pagina = $game_inicial_pagina + 8;

    // Buscando games por categoria e limite, ex: se o inicial for 0 e o por pagina for 8, vai ser do registro 0 até 7, se o inicial for alterado para 8, vai ser do registro 8 até 15...
    $games = $dados->BuscarGameCategoria($game_inicial_pagina, $games_por_pagina, "Corrida");

    // Array para guardar os jogos de uma categoria.
    $games_acao = [];

    // Buscando todos os games do banco de dados.
    $todos_games_bd = $dados->BuscarGames();

    // Passando todos os jogos de uma categoria para o array criado acima.
    foreach($todos_games_bd as $chave=>$games_bd){
        if($games_bd['categoria'] == 'Corrida'){
            $games_acao[$chave] = $games_bd;
        }
    }

    // Definindo o número de páginas
    $num_paginas = ceil(count($games_acao)/$games_por_pagina);
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
    <link rel="icon" href="imagens/satuctgames-favicon.png?<?= time() ?>">
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.8.2/css/all.css' integrity='sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay' crossorigin='anonymous'>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css?<?= time() ?>">

    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- // BOOTSTRAP CSS -->

    <title>Corrida - SatuctGames</title>
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
                                <a href="index.php" class="nav-link btn-menu-cinza">Inicio</a>
                            </li>

                            <li class="nav-item">
                                <a href="promocoes.php" class="nav-link btn-menu-cinza">Promoções</a>
                            </li>

                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle ativo" id="categorias-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">Categorias<span class="sr-only">(atual)</span></a>
                                <div class="dropdown-menu" aria-labelledby="categorias-dropdown">
                                    <a href="acao.php" class="dropdown-item">Ação</a>
                                    <a href="aventura.php" class="dropdown-item">Aventura</a>
                                    <a href="corrida.php" class="dropdown-item">Corrida</a>
                                    <a href="luta.php" class="dropdown-item">Luta</a>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="lancamentos.php" class="nav-link btn-menu-cinza">Lançamentos</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto">
                            <form class="form-inline my-lg-0 my-2" autocomplete="off">
                                <div class='form-busca d-none'>
                                    <input class="form-control form-control-sm" id="buscar" type="text" placeholder="Escreva o nome do game" aria-label="Buscar" style="border: 1px solid #e4e2e2 !important;">
                                    <button class="btn btn-buscar-form" id="fechar-busca" type="button"><i class="fas fa-times btn-busca-icon"></i></button>

                                    <div class="busca-items text-break bg-preto shadow-sm" id="busca-items"></div>
                                </div>

                                <button class="btn btn-menu-cinza btn-abrir-busca" type="button"><i class="fas fa-search btn-busca-icon"></i></button>
                            </form>

                            <li class="nav-item">
                                <a href="carrinho.php" class="nav-link btn-menu-cinza"><i class="fas fa-shopping-cart carrinho"><span class="ml-1"><?php
                                // Se houver games no carrinho, será mostrado a quantidade.
                                if(isset($_SESSION['games_no_carrinho'])){
                                    echo $_SESSION['games_no_carrinho'];
                                }else{
                                    // Se não, o valor será 0.
                                    echo '0';
                                }
                                ?></span></i></a>
                            </li>

                            <?php // Verificando se não tem ninguém logado
                            if(!isset($info)){ ?>
                            <li class="nav-item nav-usuario">
                                <a class="nav-link mt-lg-0 btn-menu-cinza" href="login.php"><i class="fas fa-user mr-1"></i>Entrar</a>
                            </li>
                      <?php // Se tem alguém logado, será mostrado um menu próprio para esse usuário.
                            }else{ ?>
                            <li class="nav-item dropdown nav-usuario">
                                <a class="nav-link dropdown-toggle mt-lg-0 btn-menu-cinza" id="menu-usuario" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button" style="cursor: pointer;"><span class="text-cinza"><?= $info['nome'] ?></span></a>
                                <div class="dropdown-menu dropdown-menu-right mb-lg-2" aria-labelledby="menu-usuario">
                                    <a href="detalhesdaconta.php" class="dropdown-item">Detalhes da conta</a>
                                <?php // Se a sessão atual for um administrador ou moderador, essa opção do menu será mostrada.
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

    <!-- GAMES DE CORRIDA -->
    <div class="container-fluid pt-4 pb-5">
        <div class="row">
            <div class="col-12">
                <div class="col-12 d-flex mt-3 mb-5 pb-5">
                    <img src="imagens/banner-corrida.png?<?= time() ?>" alt="Banner jogos de corrida" class="img-fluid mx-auto" style="box-shadow: 0px 0px 1px 0px #292c2f;">
                </div>

                <div class="row mb-5 pt-5 pb-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                    <div class="col-12">
                        <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">GAMES DE CORRIDA</span>
                    </div>
                </div>

          <?php if(count($games) > 0){ ?>
              <?php foreach($games as $g){ ?>
                        <a href="games/<?= $g['nome_url_pagina'] ?>.php" class="row mx-sm-3 mb-2" id="categoria-games">
                            <div class="col-12-lista-games d-flex flex-nowrap bg-cinza-2 px-0 mx-auto rounded">
                                <div class="px-0">
                                    <img src="<?= $g['imagem'] ?>?<?= time() ?>" alt="Imagem de <?= $g['nome_game'] ?>" class="img-fluid rounded-left" style="width: 70px; height: 70px; filter: grayscale(20%);">
                                </div>

                                <div class="d-flex flex-row flex-wrap pl-3 pr-0">
                                    <div class="flex-row align-self-center mt-2 w-100">
                                        <h6 class="text-cinza" style="font-size: 12pt; text-align: justify;"><?= $g['nome_game'] ?></h6>
                                    </div>
                                </div>

                                <div class="d-flex flex-column justify-content-center pt-2 text-right pr-3 pl-0 ml-auto">
                                    <div class="flex-row">
                            <?php if($g['promocao'] == 1){ ?>
                                        <h6 class="text-cinza" style="font-size: 11pt;">De R$<?= $g['preco_antigo'] ?></h6>
                            <?php } ?>
                                    </div>
                                    <div class="flex-row">
                                        <h6 class="text-branco" style="font-size: 12pt;">Por R$<?= $g['preco_atual'] ?></h6>
                                    </div>
                                </div>
                            </div>
                        </a>
              <?php } ?>

                <div class="row mt-4 mx-auto">
                    <div class="col-12">
                        <nav aria-label="Navegação de páginas" class="d-flex justify-content-center">
                            <ul class="pagination my-0">
                                <form method="POST" class="<?php if($pagina_atual == 0) echo 'disabled' ?> page-item">

                              <?php if($pagina_atual > 0){ ?>
                                        <input class="form-check-input d-none" type="radio" name="pagina_atual" value="<?= $pagina_anterior; ?>" checked>

                                        <input class="form-check-input d-none" type="radio" name="game_inicial_pagina" value="<?= $game_inicial_pagina_anterior; ?>" checked>
                              <?php } ?>

                                    <button type="submit" class="page-link btn-pagina <?php if($pagina_atual == 0) echo 'desabilitado' ?>" name="anterior">
                                        <span class="text-branco" style="font-size: 12pt;">Anterior</span>
                                        <span class="sr-only">Anterior</span>
                                    </button>

                                </form>

                                <li class="page-item text-branco"><a class="page-link bg-cinza-2" style="cursor: default;"><?= $pagina_atual+1; ?></a></li>

                                <form method="POST" class="<?php if($pagina_atual == $num_paginas-1) echo 'disabled' ?> page-item">

                              <?php if($pagina_atual < $num_paginas-1){ ?>
                                        <input class="form-check-input d-none" type="radio" name="pagina_atual" value="<?= $proxima_pagina; ?>" checked>

                                        <input class="form-check-input d-none" type="radio" name="game_inicial_pagina" value="<?= $game_inicial_proxima_pagina; ?>" checked>
                              <?php } ?>

                                    <button type="submit" class="page-link btn-pagina <?php if($pagina_atual == $num_paginas-1) echo 'desabilitado' ?>" name="proximo">
                                        <span class="text-branco" style="font-size: 12pt;">Próximo</span>
                                        <span class="sr-only">Próximo</span>
                                    </button>
                                    
                                </form>
                            </ul>
                        </nav>
                    </div>
                </div>
          <?php } ?>
            </div>
        </div>
    </div>
    <!-- // GAMES DE CORRIDA -->

    <!-- FOOTER -->
    <div class="container-fluid bg-preto mt-5">
        <div class="row">
            <div class="col-6 mt-3">
                <h6 class="text-branco mt-0 mb-1 ml-1" style="font-size: 13pt;">Fale conosco</h6>
                <p class="text-branco my-0 ml-1" style="font-size: 12pt;">Celular: (00) 00000-0000</p>
                <p class="text-branco my-0 ml-1" style="font-size: 12pt;">Email: satuctgames@gmail.com</p>
                <a href="faleconosco.php" class="btn-ver-detalhes my-0 ml-1" style="text-decoration: none; font-size: 11pt;">Envie mensagens por email para nós <i class="fas fa-arrow-left"></i></a>
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
                <img src="imagens/boleto-footer.png" alt="" class="img-fluid ml-1">
                <img src="imagens/pag-seguro-footer.png" alt="" class="img-fluid bg-branco rounded ml-2">
                <img src="imagens/paypal-footer.png" alt="" class="img-fluid bg-branco rounded ml-2">
            </div>

            <div class="col-lg-6 col-md-4 col-12 text-md-right mt-md-0 ml-md-0 mt-5 ml-1">
                <h6 class="text-branco mr-1 mb-3" style="font-size: 13pt;">Forma de entrega</h6>
                <img src="imagens/correios-footer.png" alt="" class="img-fluid bg-branco rounded mr-1">
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

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>