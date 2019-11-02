<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';
    
    // Se a sessão atual não for de um administrador ou moderador, o usuário será redirecionado para a página inicial
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador'])){
        header('location: index.php');
    }

    // Método para mostrar todos usuários cadastrados.
    $mostrar_usuarios = $dados->BuscarUsuarios();

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_usuario_alterardados'])){
        // Passando o id do usuário para uma sessão.
        $_SESSION['usuario_id'] = $_POST['id_usuario_alterardados'];

        // Redirecionando para a página de 'usuários alterar dados'
        header('location: paineldecontrole-usuarios-alterar-dados.php');
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_usuario_transacoes'])){
        // Passando o id do usuário para uma sessão.
        $_SESSION['usuario_id'] = $_POST['id_usuario_transacoes'];

        // Redirecionando para a página de 'usuários transacoes'
        header('location: paineldecontrole-usuarios-transacoes.php');
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_usuario_mudarsenha'])){
        // Passando o id do usuário para uma sessão.
        $_SESSION['usuario_id'] = $_POST['id_usuario_mudarsenha'];

        // Redirecionando para a página de mudar a senha
        header('location: paineldecontrole-usuarios-mudar-senha.php');
    }
    
    // Recebendo o id do usuario a partir do botão remover, e passando como parâmetro esse id para o método RemoverUsuario, depois de remover o usuario, a página será atualizada.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['excluir_usuario_id'])){
        $dados->RemoverUsuario($_POST['excluir_usuario_id']);

        echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=paineldecontrole-usuarios.php'>";
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

    <title>Painel de Controle Usuários - SatuctGames</title>
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
                <h5 class="text-preto">Usuários</h5>
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
                                <a href="paineldecontrole-usuarios.php" class="nav-link ativo">Usuários<span class="sr-only">(atual)</span></a>
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

    <!-- PAINEL DE CONTROLE USUÁRIOS -->
    <div class="container-fluid mt-5 pt-5">
        <div class="row">
            <div class="col-12">
                <div class="row mb-5 pt-5 pb-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                    <div class="col-12">
                        <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">USUÁRIOS</span>
                    </div>
                </div>
            </div>

            <div class="col-12-lista-painel-de-controle mx-auto">
                <!-- TABELA HEAD -->
                <div class="row pt-2 bg-cinza-2 text-light text-center d-flex align-items-center border-bottom-cinza">
                    <div class="col-4 px-0">
                        <h6>Nome</h6>
                    </div>

                    <div class="col-4 col-xl-5 px-0">
                        <h6>Email</h6>
                    </div>

                    <div class="col-4 col-xl-3 px-0">
                        <h6>Ações</h6>
                    </div>
                </div>
                <!-- // TABELA HEAD -->
    
                <!-- TABELA BODY -->
                <div class="row bg-branco border-left-cinza border-right-cinza">
                <?php foreach($mostrar_usuarios as $mu){
                        if($mu['id'] != 1){ ?>
                        <div class="col-4 px-0 border-right-cinza border-bottom-cinza d-flex align-items-center justify-content-start">
                            <h6 class="text-break text-cinza-2 mt-2 ml-2" style="font-size: 11pt;"><?= $mu['id'] ?>. <?= $mu['nome'] ?></h6>
                        </div>
    
                        <div class="col-4 col-xl-5 px-0 d-flex align-items-center justify-content-start border-right-cinza border-bottom-cinza">
                            <h6 class="text-break text-cinza-2 mt-2 ml-2" style="font-size: 11pt;"><?= $mu['email'] ?></h6>
                        </div>
    
                        <form method="POST" class="col-4 col-xl-3 px-0 d-flex flex-column justify-content-center align-items-center" autocomplete="off">
                            <div class="row w-100">
                                <button type="submit" class="btn btn-success w-50 py-1 border-right-cinza border-bottom-cinza" name='id_usuario_alterardados' <?php echo "value='$mu[id]'"; ?>><i class="fas fa-edit pr-2"></i>Alterar dados</button>

                                <button type="submit" class="btn btn-cinza-form w-50 py-1 border-bottom-cinza" name='id_usuario_transacoes' <?php echo "value='$mu[id]'"; ?>><i class="fas fa-database pr-2"></i>Ver transações</button>
                            </div>

                            <div class="row w-100">
                            <?php if(isset($_SESSION['id_administrador'])){ ?>
                                    <button type="submit" class="btn btn-azul-form w-50 py-1 border-right-cinza border-bottom-cinza" name="id_usuario_mudarsenha" <?php echo "value='$mu[id]'"; ?>><i class="fas fa-unlock-alt pr-2"></i>Mudar senha</button>

                                    <button type="button" class="btn btn-danger w-50 py-1 border-bottom-cinza" data-toggle="modal" data-target="#remover-usuario-modal<?= $mu['id'] ?>"><i class="fas fa-trash pr-2"></i>Remover</button>
                            <?php }else{?>
                                    <button type="submit" class="btn btn-azul-form w-100 py-1 border-right-cinza border-bottom-cinza" name="id_usuario_mudarsenha" <?php echo "value='$mu[id]'"; ?>><i class="fas fa-unlock-alt pr-2"></i>Mudar senha</button>
                            <?php }?>
                            </div>
                        </form>

                        <!-- REMOVER USUARIO -->
                        <div class='modal fade' id='remover-usuario-modal<?= $mu['id'] ?>' tabindex='-1' role='dialog' aria-label='remover-usuario-modal' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-centered'>
                                <div class='modal-content'>
                                    <div class='modal-body'>
                                        <div class='row d-flex justify-content-end mr-1'>
                                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                <span aria-hidden='true'>&times;</span>
                                            </button>
                                        </div>

                                        <div class='row d-flex justify-content-center mt-4 mb-3'>
                                            <p class='text-cinza-2'>Você tem certeza que quer remover o usuário <?= $mu['nome']?>?</p>
                                        </div>

                                        <form method="POST" class='row d-flex justify-content-center mb-2'>
                                            <button type="submit" class='btn btn-success py-1 mr-2' style='width: 60px;' name="excluir_usuario_id" value="<?= $mu['id'] ?>">Sim</button>

                                            <button type='button' class='btn btn-danger py-1 ml-2' data-dismiss='modal' style='width: 60px;'>Não</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- // REMOVER USUÁRIO -->
                    <?php }
                    } ?>
                </div>
                <!-- // TABELA BODY -->

                <div class="row my-5">
                    <div class="col-12 d-flex justify-content-center">
                        <a href="paineldecontrole-usuarios-adicionar-usuario.php" class="btn btn-cinza-form py-2 px-3"><i class="fas fa-user-plus mr-2"></i>Adicionar novo usuário</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- // PAINEL DE CONTROLE USUÁRIOS -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>