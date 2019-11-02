<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se a sessão atual não for de um administrador ou moderador, o usuário será redirecionado para a página inicial.
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador'])){
        header('location: index.php');
    }

    // Classe com métodos para várias coisas.
    $criar_cupom = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // Arrays para armazenar mensagens de erro e sucesso.
    $mensagem_erro = [];
    $mensagem_sucesso = [];

    // Se o botão 'criar-cupom' for clicado, tudo abaixo será executado.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['criar-cupom'])){
        // Recebendo dados e colocando em variáveis.
        $cupom = addslashes(strip_tags(trim($_POST['cupom'])));
        $desconto = addslashes(strip_tags(trim($_POST['desconto'])));
        
        // Verificando se cada campo está vazio.
        if(strlen($cupom) == 0){
            $mensagem_erro[] = "Preencha o campo Nome do cupom.";
        }

        if(strlen($desconto) == 0){
            $mensagem_erro[] = "Preencha o campo Porcentagem do desconto.";
        }

        // Verificando se o cupom é no mínimo de 6 caracteres.
        if(strlen($cupom) < 6){
            $mensagem_erro[] = "O Nome do cupom precisa ter no mínimo 6 caracteres.";
        }

        // Verificando se o cupom não tem nenhum espaço em branco.
        if(preg_match("/\s/", $cupom)){
            $mensagem_erro[] = "O cupom não pode conter nenhum espaço em branco";
        }

        // Se não houver mensagens de erro.
        if(count($mensagem_erro) == 0){
            // Criando um cupom apartir do nome e desconto recebido.
            if($criar_cupom->CriarCupom($cupom, $desconto)){
                $mensagem_sucesso[] = "Cupom criado com sucesso";
            }
        }
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
                <h5 class="text-preto">Cupons</h5>
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

    <!-- PAINEL DE CONTROLE CUPONS - CRIAR CUPOM -->
    <div class="container-fluid mt-5 pt-5">
            <div class="row">
                <div class="col-12">
                    <div class="row mb-3 py-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                        <div class="col-12">
                            <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">CRIAR CUPOM</span>
                        </div>
                    </div>                    

                    <div class="container-fluid mt-5 pt-4">
                        <div class="row">
                            <div class="col-xl-4 col-lg-5 col-md-6 col-sm-7 col-8 mx-auto">
                                <form method="POST">
                                    <div class="form-group">
                                        <label for="nomecupom-criar-cupom" class="text-cinza-2"><i class="fas fa-tags pr-2"></i>Nome do cupom</label>
                                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: CUPOM20" id="nomecupom-criar-cupom" name="cupom" maxlength="255">
                                        <small class="form-text text-cinza-2">Nome do cupom deve ter no mínimo 6 caracteres</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="desconto-criar-cupom" class="text-cinza-2"><i class="fas fa-percentage pr-2"></i>Porcentagem do desconto</label>
                                        <div class="input-group">
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="desconto-porcentagem-criar-cupom">%</span>
                                          </div>
                                          <input type="number" class="form-control form-control-alt-2" name="desconto" placeholder="ex: 50" id="desconto-criar-cupom" min="1" max="100">
                                        </div>
                                        <small class="form-text text-cinza-2">Desconto entre 1 e 100%</small>
                                    </div>
                
                                    <div class="btn-group w-100 mt-4">
                                        <a href="paineldecontrole-cupons.php" class="btn btn-azul-form w-50 py-2"><i class="fas fa-caret-square-left pr-2"></i>Voltar</a>
                                        <button type="submit" class="btn btn-cinza-form w-50 py-2 ml-2" name="criar-cupom"><i class="fas fa-tag pr-2"></i>Criar cupom</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                
                        <div class="row">
                            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-9 col-11 mx-auto mt-5">
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
    <!-- PAINEL DE CONTROLE CUPONS - CRIAR CUPOM -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>