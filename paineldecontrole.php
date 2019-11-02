<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se a sessão atual não for de um administrador ou moderador, o usuário será redirecionado para a página inicial
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador'])){
        header('location: index.php');
    }
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

    <title>Painel de Controle - SatuctGames</title>
</head>
<body class="bg-cinza">
    <!-- HEADER -->
    <header class="container-fluid bg-cinza">
        <div class="row">
            <div class="col-6">
                <a href="index.php">
                    <img src="imagens/satuctgames-logo.png?<?= time() ?>" alt="Logotipo do site SatuctGames" class="img-fluid logotipo mx-2 my-4">
                </a>
            </div>
            <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                <div class="d-flex flex-row">
                    <h4 class="text-preto">Painel de Controle</h4>
                </div>
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

    <!-- PAINEL DE CONTROLE -->
    <div class="container-fluid mt-3 py-5">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-4 text-right">
                        <a href="paineldecontrole-usuarios.php" class="inner">
                            <img class="img-fluid" src="imagens/img-usuarios-painel.png?<?= time() ?>">
                        </a>
                    </div>
                    <div class="col-4 text-center">
                        <a href="paineldecontrole-games.php" class="inner">
                            <img class="img-fluid" src="imagens/img-games-painel.png?<?= time() ?>"> 
                        </a>
                    </div>
                    <div class="col-4 text-left">
                        <a href="paineldecontrole-cupons.php" class="inner">
                            <img class="img-fluid" src="imagens/img-cupons-painel.png?<?= time() ?>">    
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- // PAINEL DE CONTROLE -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>