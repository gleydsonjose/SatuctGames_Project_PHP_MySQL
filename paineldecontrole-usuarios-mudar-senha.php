<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';
    
    // Se a sessão atual não for de um administrador ou moderador, o usuário será redirecionado para a página inicial.
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador'])){
        header('location: index.php');
    }

    // Classe com métodos para várias coisas.
    $mudar_senha = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // Arrays para armazenar mensagens de erro e sucesso.
    $mensagem_erro = [];
    $mensagem_sucesso = [];

    // Se existir a sessão usuario_id.
    if(isset($_SESSION['usuario_id'])){
        // Se o botão 'mudar-senha' for clicado, tudo abaixo será executado.
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mudar-senha'])){
            // Pegando os dados recebidos e armazenando em variáveis.
            $id = addslashes(strip_tags(trim($_SESSION['usuario_id'])));
            $nova_senha = addslashes(strip_tags(trim($_POST['nova-senha'])));
            $repita_nova_senha = addslashes(strip_tags(trim($_POST['repita-nova-senha'])));
            
            // Verificando se cada campo está vazio.
            if(strlen($nova_senha) == 0){
                $mensagem_erro[] = "Preencha o campo Nova senha.";
            }

            if(strlen($repita_nova_senha) == 0){
                $mensagem_erro[] = "Preencha o campo Repita nova senha.";
            }

            // Verificando se a senha está entre 8 e 24 caracteres.
            if(strlen($nova_senha) < 8 || strlen($nova_senha) > 24){
                $mensagem_erro[] = "Preencha o campo da senha corretamente.";
            }

            // Verificando se a senha tem pelo menos uma letra maiúscula.
            if(!preg_match("/[A-Z]/", $nova_senha)){
                $mensagem_erro[] = "A senha deve conter pelo menos 1 letra maiúscula.";
            }

            // Verificando se a senha tem pelo menos uma letra minúscula.
            if(!preg_match("/[a-z]/", $nova_senha)){
                $mensagem_erro[] = "A senha deve conter pelo menos 1 letra minúscula.";
            }

            // Verificando se a senha tem pelo menos um caractere especial.
            if(!preg_match("/\W/", $nova_senha)){
                $mensagem_erro[] = "A senha deve conter pelo menos 1 caractere especial, ex: +-*@.";
            }

            // Verificando se a senha não tem nenhum espaço em branco.
            if(preg_match("/\s/", $nova_senha)){
                $mensagem_erro[] = "A senha não pode conter nenhum espaço em branco";
            }

            // Verificando se as senhas são iguais.
            if(strcmp($nova_senha, $repita_nova_senha) != 0){
                $mensagem_erro[] = "As senhas não correspondem.";
            }

            // Se não houver mensagens de erro.
            if(count($mensagem_erro) == 0){
                // Mudando a senha do usuário a partir do id e removendo a sessão id.
                if($mudar_senha->MudarSenhaPorId($id, $nova_senha)){
                    $mensagem_sucesso[] = "Senha alterada com sucesso";

                    // Removendo sessão do id.
                    unset($_SESSION['usuario_id']);

                    // Redirecionando.
                    echo "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=paineldecontrole-usuarios.php'>";
                }
            }
        }
    }else{
        // Redirecionando.
        header('location: paineldecontrole-usuarios.php');
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

    <!-- PAINEL DE CONTROLE USUÁRIOS - MUDAR SENHA -->
    <div class="container-fluid mt-5 pt-5">
            <div class="row">
                <div class="col-12">
                    <div class="row mb-3 py-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                        <div class="col-12">
                            <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">MUDAR SENHA</span>
                        </div>
                    </div>

                    <div class="container-fluid mt-5 pt-4">
                        <div class="row">
                            <div class="col-xl-4 col-lg-5 col-md-6 col-sm-7 col-8 mx-auto">
                                <form method="POST" autocomplete="off">
                                    <div class="form-group">
                                        <label for="nova-senha-mudar-senha" class="text-cinza-2"><i class="fas fa-lock pr-2"></i>Nova senha</label>
                                        <input type="password" class="form-control form-control-alt-2" id="nova-senha-mudar-senha" name="nova-senha">
                                        <small class="form-text text-cinza-2">A senha deve conter entre 8 e 24 caracteres</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="repita-nova-senha-mudar-senha" class="text-cinza-2"><i class="fas fa-key pr-2"></i>Repita a nova senha</label>
                                        <input type="password" class="form-control form-control-alt-2" id="repita-nova-senha-mudar-senha" name="repita-nova-senha">
                                    </div>
                
                                    <div class="btn-group w-100 mt-4">
                                        <a href="paineldecontrole-usuarios.php" class="btn btn-azul-form w-25 py-2"><i class="fas fa-caret-square-left pr-2"></i>Voltar</a>
                                        <button type="submit" class="btn btn-cinza-form w-75 py-2 ml-2" name="mudar-senha"><i class="fas fa-unlock-alt pr-2"></i>Mudar senha</button>
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
    <!-- // PAINEL DE CONTROLE USUÁRIOS - MUDAR SENHA -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>